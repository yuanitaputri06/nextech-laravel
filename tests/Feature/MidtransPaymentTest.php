<?php

namespace Tests\Feature;

use App\Services\MidtransService;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.midtrans.server_key' => 'SB-Mid-server-TEST-KEY-123']);
        config(['services.midtrans.client_key' => 'SB-Mid-client-TEST-KEY-123']);
    }

    /**
     * Test verifikasi signature Midtrans (valid dan invalid)
     */
    public function test_signature_verification_valid_and_invalid(): void
    {
        $service = new MidtransService();
        $orderId = 'NX-TEST-001';
        $statusCode = '200';
        $grossAmount = '150000.00';
        $serverKey = 'SB-Mid-server-TEST-KEY-123';

        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $invalidSignature = 'invalid-signature-hash';

        $this->assertTrue(
            $service->verifySignature($orderId, $statusCode, $grossAmount, $validSignature),
            'Valid signature harus menghasilkan true'
        );

        $this->assertFalse(
            $service->verifySignature($orderId, $statusCode, $grossAmount, $invalidSignature),
            'Invalid signature harus menghasilkan false'
        );
    }

    /**
     * Test pemetaan status transaksi Midtrans ke status internal NexTech
     */
    public function test_parse_notification_status_mappings(): void
    {
        $service = new MidtransService();
        $orderId = 'NX-TEST-002';
        $statusCode = '200';
        $grossAmount = '250000.00';
        $serverKey = 'SB-Mid-server-TEST-KEY-123';
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // 1. Settlement
        $settlementPayload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'midtrans-trx-uuid-1',
        ];
        $result = $service->parseNotification($settlementPayload);
        $this->assertTrue($result['is_valid']);
        $this->assertEquals('Completed', $result['mapped_status']);
        $this->assertTrue($result['is_paid']);

        // 2. Capture (Credit Card accept)
        $capturePayload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
            'payment_type' => 'credit_card',
            'transaction_id' => 'midtrans-trx-uuid-2',
        ];
        $result = $service->parseNotification($capturePayload);
        $this->assertEquals('Completed', $result['mapped_status']);
        $this->assertTrue($result['is_paid']);

        // 3. Pending
        $pendingPayload = [
            'order_id' => $orderId,
            'status_code' => '201',
            'gross_amount' => $grossAmount,
            'signature_key' => hash('sha512', $orderId . '201' . $grossAmount . $serverKey),
            'transaction_status' => 'pending',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-uuid-3',
        ];
        $result = $service->parseNotification($pendingPayload);
        $this->assertEquals('Pending', $result['mapped_status']);
        $this->assertFalse($result['is_paid']);

        // 4. Expire
        $expirePayload = [
            'order_id' => $orderId,
            'status_code' => '202',
            'gross_amount' => $grossAmount,
            'signature_key' => hash('sha512', $orderId . '202' . $grossAmount . $serverKey),
            'transaction_status' => 'expire',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-uuid-4',
        ];
        $result = $service->parseNotification($expirePayload);
        $this->assertEquals('Cancelled', $result['mapped_status']);
        $this->assertTrue($result['is_cancelled']);

        // 5. Cancel
        $cancelPayload = [
            'order_id' => $orderId,
            'status_code' => '202',
            'gross_amount' => $grossAmount,
            'signature_key' => hash('sha512', $orderId . '202' . $grossAmount . $serverKey),
            'transaction_status' => 'cancel',
            'payment_type' => 'gopay',
            'transaction_id' => 'midtrans-trx-uuid-5',
        ];
        $result = $service->parseNotification($cancelPayload);
        $this->assertEquals('Cancelled', $result['mapped_status']);
        $this->assertTrue($result['is_cancelled']);
    }

    /**
     * Test webhook menolak request dengan signature palsu (HTTP 403)
     */
    public function test_webhook_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/midtrans/notification', [
            'order_id' => 'NX-NONEXISTENT',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => 'fake-invalid-signature',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test webhook mengembalikan 404 jika order_id tidak ditemukan
     */
    public function test_webhook_returns_404_for_unknown_order(): void
    {
        $orderId = 'NX-UNKNOWN-99999';
        $statusCode = '200';
        $grossAmount = '100000.00';
        $serverKey = 'SB-Mid-server-TEST-KEY-123';
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $response = $this->postJson('/midtrans/notification', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test halaman pembayaran wajib login (guest diarahkan ke login)
     */
    public function test_payment_page_requires_authentication(): void
    {
        $response = $this->get('/payment/NX-TEST-123');
        $response->assertRedirect('/login');
    }

    /**
     * Test checkout process mewajibkan login
     */
    public function test_checkout_process_requires_authentication(): void
    {
        $response = $this->post('/checkout/process', [
            'recipient_name' => 'John Doe',
            'phone' => '08123456789',
            'address' => 'Jl. Test No. 1',
            'payment_method' => 'Midtrans',
            'items' => [
                ['product_id' => 1, 'qty' => 1],
            ],
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * Test webhook settlement sukses memperbarui status pesanan menjadi Completed
     */
    public function test_webhook_settlement_updates_order_to_completed(): void
    {
        $serverKey = 'SB-Mid-server-TEST-KEY-123';
        $orderId = 'NX-UNIT-' . uniqid();
        $grossAmount = '50000.00';

        // Buat dummy order
        \Illuminate\Support\Facades\DB::table('orders')->insert([
            'user_id' => 1,
            'product_id' => 1,
            'qty' => 1,
            'trx_id' => $orderId,
            'p_status' => 'Pending',
            'payment_method' => 'Midtrans',
            'total_amt' => 50000,
            'midtrans_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $validSignature = hash('sha512', $orderId . '200' . $grossAmount . $serverKey);

        $response = $this->postJson('/midtrans/notification', [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'trx-12345',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'status' => 'Completed']);

        // Verifikasi database
        $order = \Illuminate\Support\Facades\DB::table('orders')->where('trx_id', $orderId)->first();
        $this->assertEquals('Completed', $order->p_status);
        $this->assertEquals('settlement', $order->midtrans_status);
        $this->assertEquals('qris', $order->midtrans_payment_type);
        $this->assertEquals('trx-12345', $order->midtrans_transaction_id);
        $this->assertNotNull($order->paid_at);

        // Cleanup
        \Illuminate\Support\Facades\DB::table('orders')->where('trx_id', $orderId)->delete();
    }

    /**
     * Test webhook cancel atau expire mengembalikan stok produk yang di-reserve
     */
    public function test_webhook_cancel_restores_stock(): void
    {
        $serverKey = 'SB-Mid-server-TEST-KEY-123';
        $orderId = 'NX-CANCEL-' . uniqid();
        $grossAmount = '75000.00';

        $product = \Illuminate\Support\Facades\DB::table('products')->first();
        if (!$product) {
            $this->markTestSkipped('Tidak ada produk di database.');
        }

        $initialStock = (int) $product->stock;

        // Buat dummy order 2 unit
        \Illuminate\Support\Facades\DB::table('orders')->insert([
            'user_id' => 1,
            'product_id' => $product->product_id,
            'qty' => 2,
            'trx_id' => $orderId,
            'p_status' => 'Pending',
            'payment_method' => 'Midtrans',
            'total_amt' => 75000,
            'midtrans_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $validSignature = hash('sha512', $orderId . '202' . $grossAmount . $serverKey);

        $response = $this->postJson('/midtrans/notification', [
            'order_id' => $orderId,
            'status_code' => '202',
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'expire',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'trx-expire-1',
        ]);

        $response->assertStatus(200);

        // Verifikasi status pesanan
        $order = \Illuminate\Support\Facades\DB::table('orders')->where('trx_id', $orderId)->first();
        $this->assertEquals('Cancelled', $order->p_status);

        // Verifikasi stok bertambah 2 kembali
        $updatedProduct = \Illuminate\Support\Facades\DB::table('products')->where('product_id', $product->product_id)->first();
        $this->assertEquals($initialStock + 2, (int) $updatedProduct->stock);

        // Cleanup: kembalikan stok awal dan hapus order dummy
        \Illuminate\Support\Facades\DB::table('products')->where('product_id', $product->product_id)->decrement('stock', 2);
        \Illuminate\Support\Facades\DB::table('orders')->where('trx_id', $orderId)->delete();
    }
}
