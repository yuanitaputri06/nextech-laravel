<?php

namespace App\Console\Commands;

use App\Http\Controllers\PaymentController;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimulateMidtransNotification extends Command
{
    protected $signature = 'midtrans:simulate-payment {order_id : Nomor trx_id atau order_id} {--status=settlement : Status Midtrans (settlement, pending, expire, cancel, deny)}';
    protected $description = 'Simulasikan notifikasi Webhook resmi Midtrans dengan verifikasi SHA-512 Signature Key asli untuk pengujian lokal';

    public function handle(MidtransService $midtransService, OrderService $orderService, PaymentController $paymentController): int
    {
        $orderIdInput = (string) $this->argument('order_id');
        $statusInput = (string) $this->option('status');

        $orders = DB::table('orders')->where('trx_id', $orderIdInput)->get();
        if ($orders->isEmpty() && is_numeric($orderIdInput)) {
            $orders = DB::table('orders')->where('order_id', $orderIdInput)->get();
        }

        if ($orders->isEmpty()) {
            $this->error("Pesanan dengan ID/Trx #{$orderIdInput} tidak ditemukan di database.");
            return Command::FAILURE;
        }

        $first = $orders->first();
        $actualTrxId = $first->trx_id;

        $totalAmount = (int) $orders->sum(function ($it) {
            return (int) ($it->total_amt ?: ($it->qty * ($it->product_price ?? 0)));
        });

        $serverKey = $midtransService->getServerKey();
        if (empty($serverKey)) {
            $this->error("MIDTRANS_SERVER_KEY belum diisi di file .env!");
            return Command::FAILURE;
        }

        $statusCode = in_array($statusInput, ['settlement', 'capture']) ? '200' : (in_array($statusInput, ['deny', 'expire', 'cancel']) ? '202' : '201');
        $grossAmountStr = number_format($totalAmount, 2, '.', '');
        $signature = hash('sha512', $actualTrxId . $statusCode . $grossAmountStr . $serverKey);

        $payload = [
            'order_id' => $actualTrxId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmountStr,
            'signature_key' => $signature,
            'transaction_status' => $statusInput,
            'fraud_status' => 'accept',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'sim-midtrans-' . uniqid(),
            'transaction_time' => now()->format('Y-m-d H:i:s'),
        ];

        $this->info("=== SIMULASI WEBHOOK RESMI MIDTRANS ===");
        $this->line("Trx ID        : {$actualTrxId}");
        $this->line("Gross Amount  : Rp " . number_format($totalAmount, 0, ',', '.'));
        $this->line("Status Target : {$statusInput}");
        $this->line("Signature     : " . substr($signature, 0, 16) . "...");

        $request = Request::create('/midtrans/notification', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $response = $paymentController->notification($request);
        $content = json_decode($response->getContent(), true);

        $this->newLine();
        $this->info("HTTP Status Response: " . $response->getStatusCode());
        $this->line("Response Body: " . json_encode($content, JSON_PRETTY_PRINT));

        // Verifikasi hasil database
        $updatedOrder = DB::table('orders')->where('trx_id', $actualTrxId)->first();
        $this->newLine();
        $this->info("=== HASIL DATABASE TERKINI ===");
        $this->line("Payment Status (p_status)  : " . ($updatedOrder->p_status ?? '-'));
        $this->line("Order Status (order_status): " . ($updatedOrder->order_status ?? '-'));
        $this->line("Paid At                    : " . ($updatedOrder->paid_at ?? '-'));
        $this->line("Midtrans Status            : " . ($updatedOrder->midtrans_status ?? '-'));

        return Command::SUCCESS;
    }
}
