<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected MidtransService $midtransService;
    protected OrderService $orderService;

    public function __construct(MidtransService $midtransService, OrderService $orderService)
    {
        $this->midtransService = $midtransService;
        $this->orderService = $orderService;
    }

    /**
     * Halaman Pembayaran Midtrans Snap
     * Menampilkan informasi pesanan dan memicu pembayaran Snap
     */
    public function showPayment(Request $request, $trxId)
    {
        if (!session('is_user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');

        $orders = DB::table('orders')
            ->leftJoin('products', 'orders.product_id', '=', 'products.product_id')
            ->where('orders.trx_id', $trxId)
            ->where('orders.user_id', $userId)
            ->select(
                'orders.*',
                'products.product_title',
                'products.product_image',
                'products.product_price'
            )
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('user.orders', ['tab' => 'orders'])
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $firstOrder = $orders->first();

        // Jika pesanan sudah lunas, langsung arahkan ke riwayat
        if (in_array(strtolower((string) $firstOrder->p_status), ['completed', 'selesai'])) {
            return redirect()->route('user.orders', ['tab' => 'orders'])
                ->with('success', "Pesanan #{$trxId} sudah lunas.");
        }

        // Jika pesanan sudah dibatalkan
        if (in_array(strtolower((string) $firstOrder->p_status), ['cancelled', 'batal'])) {
            return redirect()->route('user.orders', ['tab' => 'orders'])
                ->with('error', "Pesanan #{$trxId} telah dibatalkan.");
        }

        $totalAmount = $orders->sum(function ($item) {
            return (int) ($item->total_amt ?: ($item->qty * $item->product_price));
        });

        $snapToken = $firstOrder->snap_token;

        // Jika snap_token belum ada, coba buatkan
        if (empty($snapToken)) {
            if ($this->midtransService->isConfigured()) {
                try {
                    $user = DB::table('user_info')->where('user_id', $userId)->first();

                    $midtransItems = [];
                    foreach ($orders as $order) {
                        $midtransItems[] = [
                            'id' => (string) $order->product_id,
                            'price' => (int) $order->product_price,
                            'quantity' => (int) $order->qty,
                            'name' => (string) $order->product_title,
                        ];
                    }

                    $customer = [
                        'first_name' => $user->first_name ?? 'Pelanggan',
                        'last_name' => $user->last_name ?? '',
                        'email' => $user->email ?? 'user@example.com',
                        'phone' => $user->mobile ?? '',
                        'address' => $user->address1 ?? '',
                    ];

                    $snapToken = $this->midtransService->createSnapToken($trxId, $totalAmount, $midtransItems, $customer);

                    // Simpan snap token ke orders
                    DB::table('orders')->where('trx_id', $trxId)->update([
                        'snap_token' => $snapToken,
                        'updated_at' => now(),
                    ]);
                } catch (Exception $e) {
                    Log::error('Error creating snap token on showPayment: ' . $e->getMessage());
                }
            }
        }

        $clientKey = $this->midtransService->getClientKey();
        $snapUrl = $this->midtransService->getSnapUrl();
        $isConfigured = $this->midtransService->isConfigured();

        return view('user.payment', compact(
            'orders',
            'trxId',
            'totalAmount',
            'snapToken',
            'clientKey',
            'snapUrl',
            'isConfigured',
            'firstOrder'
        ));
    }

    /**
     * Halaman Hasil Pembayaran / Callback Finish Midtrans
     * Melakukan rekonsiliasi otomatis langsung ke Midtrans GET Status API jika webhook tertunda
     */
    public function paymentFinish(Request $request)
    {
        $trxId = (string) $request->query('order_id', '');
        $statusCode = (string) $request->query('status_code', '');
        $transactionStatus = (string) $request->query('transaction_status', '');

        $userId = session('user_id');

        // REKONSILIASI OTOMATIS: Ambil status resmi langsung dari server Midtrans
        if (!empty($trxId)) {
            try {
                $this->midtransService->reconcileOrder($trxId, $this->orderService);
            } catch (\Throwable $e) {
                Log::warning("Reconciliation on paymentFinish failed for Order #{$trxId}: " . $e->getMessage());
            }
        }

        $orders = collect();
        if ($trxId) {
            $orders = DB::table('orders')
                ->leftJoin('products', 'orders.product_id', '=', 'products.product_id')
                ->where('orders.trx_id', $trxId)
                ->when($userId, function ($q) use ($userId) {
                    $q->where('orders.user_id', $userId);
                })
                ->select(
                    'orders.*',
                    'products.product_title',
                    'products.product_image',
                    'products.product_price'
                )
                ->get();
        }

        $totalAmount = $orders->sum(function ($item) {
            return (int) ($item->total_amt ?: ($item->qty * $item->product_price));
        });

        return view('user.payment-finish', compact(
            'trxId',
            'statusCode',
            'transactionStatus',
            'orders',
            'totalAmount'
        ));
    }

    /**
     * Manual Trigger / Fallback Endpoint untuk Cek Status Pembayaran ke Midtrans API
     */
    public function checkStatus(Request $request, $trxId)
    {
        if (!session('is_user') && !session('is_admin')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $result = $this->midtransService->reconcileOrder($trxId, $this->orderService);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Webhook / Payment Notification URL dari Server Midtrans
     * Menerima notifikasi status pembayaran dan memperbarui database secara otomatis
     */
    public function notification(Request $request): JsonResponse
    {
        // Support method GET untuk pengecekan ketersediaan webhook (misal oleh dashboard/browser)
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Midtrans Webhook endpoint is active and listening for POST notifications.',
                'timestamp' => now()->toIso8601String(),
            ], 200);
        }

        $payload = $request->json()->all() ?: $request->all();

        // Logging notifikasi tanpa mencatat signature key secara utuh demi keamanan
        $safeLogPayload = $payload;
        if (isset($safeLogPayload['signature_key'])) {
            $safeLogPayload['signature_key'] = substr((string) $safeLogPayload['signature_key'], 0, 8) . '...';
        }
        Log::info('Midtrans Webhook Notification Received:', $safeLogPayload);

        $orderId = (string) ($payload['order_id'] ?? '');
        if (empty($orderId)) {
            return response()->json(['message' => 'Missing order_id'], 400);
        }

        $parsed = $this->midtransService->parseNotification($payload);

        // Validasi signature
        if (!$parsed['is_valid']) {
            Log::warning('Midtrans Webhook: Invalid Signature for Order #' . $orderId, [
                'order_id' => $orderId,
                'signature_prefix' => substr((string) ($payload['signature_key'] ?? ''), 0, 8) . '...',
            ]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // Ambil data pesanan berdasarkan trx_id atau order_id
        $orders = DB::table('orders')->where('trx_id', $orderId)->get();
        if ($orders->isEmpty() && is_numeric($orderId)) {
            $orders = DB::table('orders')->where('order_id', $orderId)->get();
        }

        if ($orders->isEmpty()) {
            Log::warning('Midtrans Webhook: Order #' . $orderId . ' not found in database.');
            return response()->json(['message' => 'Order not found'], 404);
        }

        $firstOrder = $orders->first();
        $actualTrxId = $firstOrder->trx_id;

        // Validasi nominal pembayaran (Gross Amount Check)
        $expectedTotal = (int) $orders->sum(function ($it) {
            return (int) ($it->total_amt ?: ($it->qty * ($it->product_price ?? 0)));
        });
        $grossAmountReceived = (int) round((float) ($payload['gross_amount'] ?? 0));
        if ($expectedTotal > 0 && abs($expectedTotal - $grossAmountReceived) > 1) {
            Log::warning("Midtrans Webhook: Gross amount mismatch for Order #{$actualTrxId}. DB: {$expectedTotal}, Payload: {$grossAmountReceived}");
            return response()->json([
                'message' => 'Gross amount mismatch',
                'expected' => $expectedTotal,
                'received' => $grossAmountReceived,
            ], 400);
        }

        // Idempotensi: Jika pesanan sudah berstatus lunas / completed, kirim response 200 tanpa pemrosesan ganda
        $currentPStatus = strtolower((string) ($firstOrder->p_status ?? ''));
        $isAlreadyPaid = in_array($currentPStatus, ['completed', 'selesai', 'paid']);
        if ($parsed['is_paid'] && $isAlreadyPaid) {
            Log::info("Midtrans Webhook: Order #{$actualTrxId} already marked as PAID (Idempotent response).");
            return response()->json([
                'success' => true,
                'message' => 'Order already processed (idempotent)',
                'order_id' => $actualTrxId,
                'payment_status' => 'Completed',
                'order_status' => $firstOrder->order_status,
            ], 200);
        }

        $newStatus = $parsed['mapped_status'];
        $midtransStatus = $parsed['transaction_status'];
        $paymentType = $parsed['payment_type'];
        $transactionId = $parsed['transaction_id'];

        try {
            if ($parsed['is_paid']) {
                // Pembayaran BERHASIL (Settlement / Capture Accept)
                $this->orderService->markPaymentSuccess($actualTrxId, $paymentType, $transactionId, $midtransStatus);
                Log::info("Midtrans Webhook: Order #{$actualTrxId} marked as COMPLETED / TERBAYAR.");
            } elseif ($parsed['is_cancelled']) {
                // Pembayaran DIBATALKAN / EXPIRED / DENY
                $this->orderService->cancelOrder($actualTrxId, "Midtrans: {$midtransStatus}", 'system', null, 'Midtrans Webhook', $midtransStatus);
                Log::info("Midtrans Webhook: Order #{$actualTrxId} marked as CANCELLED and stock restored.");
            } else {
                // Status PENDING
                DB::table('orders')->where('trx_id', $actualTrxId)->update([
                    'midtrans_status' => $midtransStatus,
                    'midtrans_payment_type' => $paymentType,
                    'midtrans_transaction_id' => $transactionId,
                    'updated_at' => now(),
                ]);

                Log::info("Midtrans Webhook: Order #{$actualTrxId} status is PENDING.");
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification successfully processed',
                'order_id' => $actualTrxId,
                'status' => $newStatus,
                'payment_status' => $parsed['is_paid'] ? 'Completed' : ($parsed['is_cancelled'] ? 'Cancelled' : 'Pending'),
                'order_status' => $parsed['is_paid'] ? 'pembayaran_berhasil' : ($parsed['is_cancelled'] ? 'dibatalkan' : 'menunggu_pembayaran'),
            ], 200);

        } catch (Exception $e) {
            Log::error("Midtrans Webhook Processing Exception for Order #{$actualTrxId}: " . $e->getMessage());
            return response()->json(['message' => 'Error processing notification: ' . $e->getMessage()], 500);
        }
    }
}
