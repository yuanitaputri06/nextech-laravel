<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $this->initConfig();
    }

    /**
     * Inisialisasi konfigurasi Midtrans
     */
    public function initConfig(): void
    {
        Config::$serverKey = trim((string) config('services.midtrans.server_key', ''));
        Config::$clientKey = trim((string) config('services.midtrans.client_key', ''));
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);
    }

    /**
     * Cek apakah Midtrans Server Key dan Client Key sudah diisi di .env
     */
    public function isConfigured(): bool
    {
        $serverKey = trim((string) config('services.midtrans.server_key', ''));
        $clientKey = trim((string) config('services.midtrans.client_key', ''));

        return !empty($serverKey) && !empty($clientKey);
    }

    /**
     * Dapatkan Server Key
     */
    public function getServerKey(): string
    {
        return trim((string) config('services.midtrans.server_key', ''));
    }

    /**
     * Dapatkan Client Key untuk Snap frontend
     */
    public function getClientKey(): string
    {
        return trim((string) config('services.midtrans.client_key', ''));
    }

    /**
     * Dapatkan URL script Snap JS berdasarkan environment (Sandbox / Production)
     */
    public function getSnapUrl(): string
    {
        return config('services.midtrans.snap_url', 'https://app.sandbox.midtrans.com/snap/snap.js');
    }

    /**
     * Dapatkan Base URL API Midtrans
     */
    public function getApiBaseUrl(): string
    {
        $isProduction = (bool) config('services.midtrans.is_production', false);
        return $isProduction
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';
    }

    /**
     * Buat Snap Token melalui Midtrans Snap API
     */
    public function createSnapToken(string $orderId, int $grossAmount, array $items, array $customer, array $overrideParams = []): string
    {
        if (!$this->isConfigured()) {
            throw new Exception('Kredensial Midtrans belum dikonfigurasi. Harap isi MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY pada file .env.');
        }

        $this->initConfig();

        // Siapkan item details yang valid
        $itemDetails = [];
        $calculatedTotal = 0;

        foreach ($items as $item) {
            $itemPrice = (int) ($item['price'] ?? 0);
            $itemQty = (int) ($item['quantity'] ?? 1);
            $itemSubtotal = $itemPrice * $itemQty;
            $calculatedTotal += $itemSubtotal;

            $itemDetails[] = [
                'id' => (string) ($item['id'] ?? 'item-' . rand(100, 999)),
                'price' => $itemPrice,
                'quantity' => $itemQty,
                'name' => mb_substr(strip_tags((string) ($item['name'] ?? 'Produk')), 0, 50),
            ];
        }

        $finalAmount = $calculatedTotal > 0 ? $calculatedTotal : $grossAmount;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $finalAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => mb_substr((string) ($customer['first_name'] ?? 'Pelanggan'), 0, 50),
                'last_name' => mb_substr((string) ($customer['last_name'] ?? ''), 0, 50),
                'email' => (string) ($customer['email'] ?? 'customer@example.com'),
                'phone' => (string) ($customer['phone'] ?? ''),
                'billing_address' => [
                    'first_name' => (string) ($customer['first_name'] ?? 'Pelanggan'),
                    'last_name' => (string) ($customer['last_name'] ?? ''),
                    'email' => (string) ($customer['email'] ?? 'customer@example.com'),
                    'phone' => (string) ($customer['phone'] ?? ''),
                    'address' => (string) ($customer['address'] ?? ''),
                ],
                'shipping_address' => [
                    'first_name' => (string) ($customer['recipient_name'] ?? $customer['first_name'] ?? 'Penerima'),
                    'last_name' => '',
                    'email' => (string) ($customer['email'] ?? 'customer@example.com'),
                    'phone' => (string) ($customer['phone'] ?? ''),
                    'address' => (string) ($customer['address'] ?? ''),
                ],
            ],
            'callbacks' => [
                'finish' => route('user.payment.finish'),
            ],
            'expiry' => [
                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        if (!empty($overrideParams)) {
            $params = array_replace_recursive($params, $overrideParams);
        }

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (Exception $e) {
            Log::error('Midtrans Snap Token Creation Failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'params' => $params,
            ]);
            throw new Exception('Gagal membuat sesi pembayaran Midtrans: ' . $e->getMessage());
        }
    }

    /**
     * Validasi Signature Key Webhook Midtrans
     * Rumus resmi Midtrans: SHA512(order_id + status_code + gross_amount + ServerKey)
     * Mendukung variasi format desimal nominal
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = trim((string) config('services.midtrans.server_key', ''));
        if (empty($serverKey)) {
            return false;
        }

        // 1. Cek string exact seperti yang diterima dari Midtrans
        $expected1 = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (hash_equals($expected1, $signatureKey)) {
            return true;
        }

        // 2. Cek dengan format 2 desimal jika gross_amount bulat (misal: 150000 -> 150000.00)
        $formatted2Dec = number_format((float) $grossAmount, 2, '.', '');
        $expected2 = hash('sha512', $orderId . $statusCode . $formatted2Dec . $serverKey);
        if (hash_equals($expected2, $signatureKey)) {
            return true;
        }

        // 3. Cek format bulat jika gross_amount berdesimal (misal: 150000.00 -> 150000)
        $intVal = (string) ((int) $grossAmount);
        $expected3 = hash('sha512', $orderId . $statusCode . $intVal . $serverKey);
        if (hash_equals($expected3, $signatureKey)) {
            return true;
        }

        return false;
    }

    /**
     * Memproses data notifikasi dari Midtrans Webhook
     */
    public function parseNotification(array $payload): array
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? 'accept');
        $paymentType = (string) ($payload['payment_type'] ?? 'midtrans');
        $transactionId = (string) ($payload['transaction_id'] ?? '');

        // Validasi signature
        $isValidSignature = $this->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey);

        // Petakan status Midtrans ke status internal NexTech
        $orderStatus = 'Pending';
        $isPaid = false;
        $isCancelled = false;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $orderStatus = 'Pending';
            } elseif ($fraudStatus === 'accept') {
                $orderStatus = 'Completed';
                $isPaid = true;
            }
        } elseif ($transactionStatus === 'settlement') {
            $orderStatus = 'Completed';
            $isPaid = true;
        } elseif ($transactionStatus === 'pending') {
            $orderStatus = 'Pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $orderStatus = 'Cancelled';
            $isCancelled = true;
        }

        return [
            'is_valid' => $isValidSignature,
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payment_type' => $paymentType,
            'transaction_id' => $transactionId,
            'mapped_status' => $orderStatus,
            'is_paid' => $isPaid,
            'is_cancelled' => $isCancelled,
        ];
    }

    /**
     * Panggil Midtrans GET Status API untuk memeriksa status transaksi resmi di server Midtrans
     * GET https://api.sandbox.midtrans.com/v2/{order_id}/status
     */
    public function getTransactionStatus(string $orderId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $this->initConfig();
        $serverKey = $this->getServerKey();
        $url = $this->getApiBaseUrl() . urlencode($orderId) . '/status';

        try {
            // Panggilan HTTP resmi dengan Basic Auth (ServerKey:)
            $response = Http::withBasicAuth($serverKey, '')
                ->acceptJson()
                ->timeout(12)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("Midtrans GET Status HTTP {$response->status()} for Order #{$orderId}: " . $response->body());
        } catch (\Throwable $e) {
            Log::warning("Midtrans HTTP GET Status failed for Order #{$orderId}: " . $e->getMessage());

            // Fallback via Midtrans SDK
            try {
                $sdkResponse = \Midtrans\Transaction::status($orderId);
                return (array) $sdkResponse;
            } catch (\Throwable $sdkEx) {
                Log::error("Midtrans SDK Transaction::status failed for Order #{$orderId}: " . $sdkEx->getMessage());
            }
        }

        return null;
    }

    /**
     * Rekonsiliasi Status Pembayaran dengan Midtrans GET Status API
     * Mengambil status transaksi langsung dari server Midtrans dan memperbarui database jika status berubah
     */
    public function reconcileOrder(string $orderId, OrderService $orderService): array
    {
        $statusData = $this->getTransactionStatus($orderId);

        if (!$statusData) {
            return [
                'success' => false,
                'message' => "Tidak dapat terhubung ke Midtrans API untuk pesanan #{$orderId}.",
                'synced' => false,
            ];
        }

        $statusCode = (string) ($statusData['status_code'] ?? '');
        $transactionStatus = (string) ($statusData['transaction_status'] ?? '');
        $fraudStatus = (string) ($statusData['fraud_status'] ?? 'accept');
        $paymentType = (string) ($statusData['payment_type'] ?? 'midtrans');
        $transactionId = (string) ($statusData['transaction_id'] ?? '');

        $isPaid = false;
        $isCancelled = false;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $isPaid = true;
            }
        } elseif ($transactionStatus === 'settlement') {
            $isPaid = true;
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $isCancelled = true;
        }

        $order = DB::table('orders')->where('trx_id', $orderId)->first();
        if (!$order && is_numeric($orderId)) {
            $order = DB::table('orders')->where('order_id', $orderId)->first();
        }

        if (!$order) {
            return [
                'success' => false,
                'message' => "Pesanan #{$orderId} tidak ditemukan di database.",
                'synced' => false,
            ];
        }

        $actualTrxId = $order->trx_id;
        $currentPaymentStatus = strtolower((string) ($order->p_status ?? ''));

        if ($isPaid) {
            $orderService->markPaymentSuccess($actualTrxId, $paymentType, $transactionId, $transactionStatus);
            Log::info("Reconciliation SUCCESS: Order #{$actualTrxId} marked as COMPLETED via Midtrans Status API.");

            return [
                'success' => true,
                'synced' => true,
                'is_paid' => true,
                'status' => 'Completed',
                'order_status' => 'pembayaran_berhasil',
                'transaction_status' => $transactionStatus,
                'message' => "Pembayaran berhasil diverifikasi langsung dari Midtrans ({$transactionStatus}). Status pembayaran: Terbayar.",
            ];
        } elseif ($isCancelled) {
            if ($currentPaymentStatus !== 'cancelled' && $currentPaymentStatus !== 'batal') {
                $orderService->cancelOrder($actualTrxId, "Midtrans: {$transactionStatus}", 'system', null, 'Midtrans Status API', $transactionStatus);
            }

            return [
                'success' => true,
                'synced' => true,
                'is_paid' => false,
                'status' => 'Cancelled',
                'order_status' => 'dibatalkan',
                'transaction_status' => $transactionStatus,
                'message' => "Transaksi di Midtrans berstatus {$transactionStatus}. Pesanan dibatalkan dan stok dikembalikan.",
            ];
        } else {
            // Status PENDING
            DB::table('orders')->where('trx_id', $actualTrxId)->update([
                'midtrans_status' => $transactionStatus,
                'midtrans_payment_type' => $paymentType,
                'midtrans_transaction_id' => $transactionId,
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'synced' => true,
                'is_paid' => false,
                'status' => 'Pending',
                'order_status' => $order->order_status,
                'transaction_status' => $transactionStatus,
                'message' => "Status pembayaran di Midtrans masih Pending (menunggu pembayaran diselesaikan oleh pembeli).",
            ];
        }
    }
}
