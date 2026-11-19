<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    protected string $serverKey = 'SB-Mid-server-TEST-KEY-123';
    protected string $clientKey = 'SB-Mid-client-TEST-KEY-123';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.midtrans.server_key' => $this->serverKey]);
        config(['services.midtrans.client_key' => $this->clientKey]);
    }

    /**
     * Helper untuk mendapatkan produk uji coba
     */
    protected function getOrCreateTestProduct(): object
    {
        $product = DB::table('products')->first();
        if (!$product) {
            $catId = DB::table('categories')->insertGetId([
                'cat_title' => 'Test Cat ' . uniqid(),
            ]);
            $brandId = DB::table('brands')->insertGetId([
                'brand_title' => 'Test Brand ' . uniqid(),
            ]);
            $prodId = DB::table('products')->insertGetId([
                'product_cat' => $catId,
                'product_brand' => $brandId,
                'product_title' => 'Test Laptop ' . uniqid(),
                'product_price' => 15000000,
                'product_desc' => 'Deskripsi Laptop Uji Coba',
                'product_image' => 'laptop.jpg',
                'product_keywords' => 'test laptop',
                'stock' => 10,
            ]);
            return DB::table('products')->where('product_id', $prodId)->first();
        }
        return $product;
    }

    /**
     * Helper untuk mendapatkan user uji coba
     */
    protected function getOrCreateTestUser(): object
    {
        $user = DB::table('user_info')->first();
        if (!$user) {
            $userId = DB::table('user_info')->insertGetId([
                'first_name' => 'Budi',
                'last_name' => 'Santoso',
                'email' => 'budi_' . uniqid() . '@example.com',
                'password' => md5('password123'),
                'mobile' => '081234567890',
                'address1' => 'Jl. Jenderal Sudirman No. 45 Jakarta',
                'address2' => '',
            ]);
            return DB::table('user_info')->where('user_id', $userId)->first();
        }
        return $user;
    }

    /**
     * Skenario 1: Checkout menghasilkan status Menunggu Pembayaran dan mencatat riwayat
     */
    public function test_checkout_creates_order_with_menunggu_pembayaran_and_records_history(): void
    {
        $user = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestTestProduct();

        $trxId = 'NX-TEST-CK-' . uniqid();

        // Simpan pesanan langsung via DB seperti alur checkout
        DB::table('orders')->insert([
            'user_id' => $user->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxId,
            'p_status' => 'Pending',
            'order_status' => 'menunggu_pembayaran',
            'payment_method' => 'Midtrans',
            'total_amt' => (int) $product->product_price,
            'midtrans_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orderService = app(OrderService::class);
        $orderService->recordHistory(
            trxId: $trxId,
            status: 'menunggu_pembayaran',
            title: 'Pesanan Dibuat',
            description: "Pesanan #{$trxId} berhasil dibuat. Menunggu konfirmasi pembayaran via Midtrans Snap.",
            actorType: 'user',
            actorId: $user->user_id,
            actorName: $user->first_name . ' ' . $user->last_name
        );

        $order = DB::table('orders')->where('trx_id', $trxId)->first();
        $this->assertEquals('menunggu_pembayaran', $order->order_status);
        $this->assertEquals('Pending', $order->p_status);

        $history = DB::table('order_status_histories')->where('trx_id', $trxId)->first();
        $this->assertNotNull($history);
        $this->assertEquals('menunggu_pembayaran', $history->status);

        // Cleanup
        DB::table('order_status_histories')->where('trx_id', $trxId)->delete();
        DB::table('orders')->where('trx_id', $trxId)->delete();
    }

    protected function getOrCreateTestTestProduct(): object
    {
        return $this->getOrCreateTestProduct();
    }

    /**
     * Skenario 2: Admin melihat pesanan dengan status Menunggu Pembayaran
     */
    public function test_admin_orders_page_displays_pending_orders(): void
    {
        $user = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestProduct();
        $trxId = 'NX-ADM-' . uniqid();

        DB::table('orders')->insert([
            'user_id' => $user->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxId,
            'p_status' => 'Pending',
            'order_status' => 'menunggu_pembayaran',
            'payment_method' => 'Midtrans',
            'total_amt' => (int) $product->product_price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withSession([
            'admin_id' => 1,
            'admin_name' => 'Admin Utama',
            'is_admin' => true,
        ])->get('/admin/orders');

        $response->assertStatus(200);
        $response->assertSee($trxId);
        $response->assertSee('Menunggu Pembayaran');

        // Cleanup
        DB::table('orders')->where('trx_id', $trxId)->delete();
    }

    /**
     * Skenario 3 & 4: Webhook Midtrans Settlement mengubah status pembayaran menjadi Lunas
     * dan order_status menjadi 'pembayaran_berhasil' (Siap Diproses)
     */
    public function test_midtrans_webhook_settlement_updates_order_status_to_pembayaran_berhasil(): void
    {
        $user = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestProduct();
        $trxId = 'NX-HOOK-' . uniqid();
        $grossAmount = number_format($product->product_price, 2, '.', '');

        DB::table('orders')->insert([
            'user_id' => $user->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxId,
            'p_status' => 'Pending',
            'order_status' => 'menunggu_pembayaran',
            'payment_method' => 'Midtrans',
            'total_amt' => (int) $product->product_price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $validSignature = hash('sha512', $trxId . '200' . $grossAmount . $this->serverKey);

        $response = $this->postJson('/midtrans/notification', [
            'order_id' => $trxId,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-' . uniqid(),
        ]);

        $response->assertStatus(200);

        // Verifikasi tabel orders
        $order = DB::table('orders')->where('trx_id', $trxId)->first();
        $this->assertEquals('Completed', $order->p_status);
        $this->assertEquals('pembayaran_berhasil', $order->order_status);
        $this->assertNotNull($order->paid_at);

        // Verifikasi tabel order_status_histories
        $history = DB::table('order_status_histories')
            ->where('trx_id', $trxId)
            ->where('status', 'pembayaran_berhasil')
            ->first();
        $this->assertNotNull($history);
        $this->assertEquals('Pembayaran Berhasil', $history->title);

        // Cleanup
        DB::table('order_status_histories')->where('trx_id', $trxId)->delete();
        DB::table('orders')->where('trx_id', $trxId)->delete();
    }

    /**
     * Skenario: Admin TIDAK DAPAT memproses pesanan yang masih 'menunggu_pembayaran'
     */
    public function test_admin_cannot_process_order_while_still_menunggu_pembayaran(): void
    {
        $user = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestProduct();
        $trxId = 'NX-UNPAID-' . uniqid();

        DB::table('orders')->insert([
            'user_id' => $user->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxId,
            'p_status' => 'Pending',
            'order_status' => 'menunggu_pembayaran',
            'payment_method' => 'Midtrans',
            'total_amt' => (int) $product->product_price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withSession([
            'admin_id' => 1,
            'admin_name' => 'Admin NexTech',
            'is_admin' => true,
        ])->post("/admin/orders/{$trxId}/process");

        $response->assertSessionHas('error');

        $order = DB::table('orders')->where('trx_id', $trxId)->first();
        $this->assertEquals('menunggu_pembayaran', $order->order_status);

        // Cleanup
        DB::table('orders')->where('trx_id', $trxId)->delete();
    }

    /**
     * Skenario 5 & 6: Admin mengklik Proses Pesanan (status berubah menjadi Sedang Diproses)
     * dan User melihat status Sedang Diproses
     */
    public function test_admin_processes_order_and_user_sees_sedang_diproses(): void
    {
        $user = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestProduct();
        $trxId = 'NX-PROC-' . uniqid();

        DB::table('orders')->insert([
            'user_id' => $user->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxId,
            'p_status' => 'Completed',
            'order_status' => 'pembayaran_berhasil',
            'payment_method' => 'Midtrans',
            'total_amt' => (int) $product->product_price,
            'paid_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin memproses pesanan
        $responseAdmin = $this->withSession([
            'admin_id' => 1,
            'admin_name' => 'Admin NexTech',
            'is_admin' => true,
        ])->post("/admin/orders/{$trxId}/process");

        $responseAdmin->assertSessionHas('success');

        $order = DB::table('orders')->where('trx_id', $trxId)->first();
        $this->assertEquals('sedang_diproses', $order->order_status);
        $this->assertNotNull($order->processed_at);

        // Verifikasi User melihat perubahan status
        $responseUser = $this->withSession([
            'user_id' => $user->user_id,
            'user_name' => $user->first_name,
            'is_user' => true,
        ])->get("/user/orders/{$trxId}");

        $responseUser->assertStatus(200);
        $responseUser->assertSee('Sedang Diproses');

        // Cleanup
        DB::table('order_status_histories')->where('trx_id', $trxId)->delete();
        DB::table('orders')->where('trx_id', $trxId)->delete();
    }

    /**
     * Skenario 7 & 8: Admin mengisi kurir dan nomor resi kemudian mengklik Kirim Pesanan
     * (status berubah menjadi Dikirim), dan User melihat informasi pengiriman
     */
    public function test_admin_ships_order_and_user_sees_courier_and_tracking_info(): void
    {
        $user = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestProduct();
        $trxId = 'NX-SHIP-' . uniqid();

        DB::table('orders')->insert([
            'user_id' => $user->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxId,
            'p_status' => 'Completed',
            'order_status' => 'sedang_diproses',
            'payment_method' => 'Midtrans',
            'total_amt' => (int) $product->product_price,
            'processed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin mengirim pesanan
        $responseAdmin = $this->withSession([
            'admin_id' => 1,
            'admin_name' => 'Admin NexTech',
            'is_admin' => true,
        ])->post("/admin/orders/{$trxId}/ship", [
            'courier_name' => 'JNE Regular',
            'tracking_number' => 'NX-JNE-99887766',
            'shipping_notes' => 'Dikemas dengan kayu dan bubble wrap tebal',
        ]);

        $responseAdmin->assertSessionHas('success');

        $order = DB::table('orders')->where('trx_id', $trxId)->first();
        $this->assertEquals('dikirim', $order->order_status);
        $this->assertEquals('JNE Regular', $order->courier_name);
        $this->assertEquals('NX-JNE-99887766', $order->tracking_number);
        $this->assertNotNull($order->shipped_at);

        // User melihat status Dikirim beserta kurir dan resi
        $responseUser = $this->withSession([
            'user_id' => $user->user_id,
            'user_name' => $user->first_name,
            'is_user' => true,
        ])->get("/user/orders/{$trxId}");

        $responseUser->assertStatus(200);
        $responseUser->assertSee('Sedang Dikirim');
        $responseUser->assertSee('JNE Regular');
        $responseUser->assertSee('NX-JNE-99887766');
        $responseUser->assertSee('Pesanan Diterima'); // Tombol Pesanan Diterima muncul

        // Cleanup
        DB::table('order_status_histories')->where('trx_id', $trxId)->delete();
        DB::table('orders')->where('trx_id', $trxId)->delete();
    }

    /**
     * Skenario 9 & 10: User mengklik Pesanan Diterima -> status berubah menjadi Selesai
     * pada sisi User dan Admin, serta mencatat completed_at
     */
    public function test_user_confirms_received_order_becomes_selesai(): void
    {
        $user = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestProduct();
        $trxId = 'NX-RCV-' . uniqid();

        DB::table('orders')->insert([
            'user_id' => $user->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxId,
            'p_status' => 'Completed',
            'order_status' => 'dikirim',
            'payment_method' => 'Midtrans',
            'courier_name' => 'SiCepat REG',
            'tracking_number' => 'SC-12345678',
            'total_amt' => (int) $product->product_price,
            'shipped_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User konfirmasi pesanan diterima
        $responseUser = $this->withSession([
            'user_id' => $user->user_id,
            'user_name' => $user->first_name,
            'is_user' => true,
        ])->post("/user/orders/{$trxId}/confirm-received");

        $responseUser->assertRedirect('/user/orders?tab=orders');
        $responseUser->assertSessionHas('success');

        $order = DB::table('orders')->where('trx_id', $trxId)->first();
        $this->assertEquals('selesai', $order->order_status);
        $this->assertNotNull($order->completed_at);

        // Riwayat tercatat
        $history = DB::table('order_status_histories')
            ->where('trx_id', $trxId)
            ->where('status', 'selesai')
            ->first();
        $this->assertNotNull($history);
        $this->assertEquals('user', $history->actor_type);

        // Sisi Admin melihat status Selesai
        $responseAdmin = $this->withSession([
            'admin_id' => 1,
            'admin_name' => 'Admin NexTech',
            'is_admin' => true,
        ])->get("/admin/orders/{$trxId}/detail");

        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Pesanan Selesai');

        // Cleanup
        DB::table('order_status_histories')->where('trx_id', $trxId)->delete();
        DB::table('orders')->where('trx_id', $trxId)->delete();
    }

    /**
     * Skenario Keamanan: User tidak dapat melihat atau mengonfirmasi pesanan milik user lain
     */
    public function test_user_cannot_access_or_confirm_another_users_order(): void
    {
        $user1 = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestProduct();
        $trxId = 'NX-SEC-' . uniqid();

        DB::table('orders')->insert([
            'user_id' => $user1->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxId,
            'p_status' => 'Completed',
            'order_status' => 'dikirim',
            'payment_method' => 'Midtrans',
            'total_amt' => (int) $product->product_price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User 2 mencoba melihat detail pesanan User 1
        $otherUserId = $user1->user_id + 999;
        $responseView = $this->withSession([
            'user_id' => $otherUserId,
            'user_name' => 'Stranger',
            'is_user' => true,
        ])->get("/user/orders/{$trxId}");

        $responseView->assertRedirect('/user/orders?tab=orders');
        $responseView->assertSessionHas('error');

        // User 2 mencoba konfirmasi terima pesanan User 1
        $responseConfirm = $this->withSession([
            'user_id' => $otherUserId,
            'user_name' => 'Stranger',
            'is_user' => true,
        ])->post("/user/orders/{$trxId}/confirm-received");

        $responseConfirm->assertSessionHas('error');

        // Status order tetap 'dikirim'
        $order = DB::table('orders')->where('trx_id', $trxId)->first();
        $this->assertEquals('dikirim', $order->order_status);

        // Cleanup
        DB::table('orders')->where('trx_id', $trxId)->delete();
    }

    /**
     * Skenario Filter & Pencarian Admin
     */
    public function test_admin_search_and_filter(): void
    {
        $user = $this->getOrCreateTestUser();
        $product = $this->getOrCreateTestProduct();
        $trxUnique = 'NX-SEARCH-' . uniqid();

        DB::table('orders')->insert([
            'user_id' => $user->user_id,
            'product_id' => $product->product_id,
            'qty' => 1,
            'trx_id' => $trxUnique,
            'p_status' => 'Pending',
            'order_status' => 'menunggu_pembayaran',
            'payment_method' => 'Midtrans',
            'total_amt' => (int) $product->product_price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Cari dengan parameter q
        $response = $this->withSession([
            'admin_id' => 1,
            'admin_name' => 'Admin NexTech',
            'is_admin' => true,
        ])->get("/admin/orders?q={$trxUnique}");

        $response->assertStatus(200);
        $response->assertSee($trxUnique);

        // Filter status order_status
        $responseFilter = $this->withSession([
            'admin_id' => 1,
            'admin_name' => 'Admin NexTech',
            'is_admin' => true,
        ])->get('/admin/orders?order_status=menunggu_pembayaran');

        $responseFilter->assertStatus(200);
        $responseFilter->assertSee($trxUnique);

        // Cleanup
        DB::table('orders')->where('trx_id', $trxUnique)->delete();
    }
}
