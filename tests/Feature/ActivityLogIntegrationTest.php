<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActivityLogIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'ecommerce',
        ]);
        DB::purge('mysql');
    }

    /**
     * Skenario 1 & 5 & 6: Admin berhasil menambahkan produk dan aktivitas tercatat ke ecommerce_logs.
     */
    public function test_admin_can_create_product_and_activity_is_logged(): void
    {
        $adminSession = [
            'admin_id'   => 1,
            'admin_name' => 'Admin Utama',
            'is_admin'   => true,
        ];

        // Pastikan ada kategori untuk produk
        $cat = DB::connection('mysql')->table('categories')->first();
        $catId = $cat ? $cat->cat_id : 1;

        $productData = [
            'product_title'    => 'Produk Uji Integrasi Log',
            'product_brand'    => 1,
            'product_price'    => 150000,
            'product_cat'      => $catId,
            'product_desc'     => 'Deskripsi pengujian logging',
            'product_keywords' => 'uji,log,dualdb',
        ];

        $response = $this->withSession($adminSession)
            ->post(route('admin.products.store'), $productData);

        $response->assertRedirect(route('admin.products'));
        $response->assertSessionHas('success');

        // Pastikan produk masuk ke database utama (ecommerce)
        $createdProduct = DB::connection('mysql')->table('products')
            ->where('product_title', 'Produk Uji Integrasi Log')
            ->orderBy('product_id', 'desc')
            ->first();

        $this->assertNotNull($createdProduct);

        // Pastikan tercatat di database kedua (ecommerce_logs)
        $log = ActivityLog::where('subject_type', 'Product')
            ->where('subject_id', $createdProduct->product_id)
            ->where('activity_type', 'Menambahkan Produk')
            ->first();

        $this->assertNotNull($log, 'Log aktivitas penambahan produk harus tercatat.');
        $this->assertEquals(1, $log->admin_id);
        $this->assertEquals('Admin Utama', $log->admin_name);
        $this->assertStringContainsString('Produk Uji Integrasi Log', $log->description);
        $this->assertNotNull($log->created_at);

        // Simpan createdProduct id untuk tes berikutnya atau bersihkan
        $this->productId = $createdProduct->product_id;
    }

    /**
     * Skenario 2 & 5 & 6: Admin berhasil mengubah produk dan aktivitas tercatat.
     */
    public function test_admin_can_update_product_and_activity_is_logged(): void
    {
        $adminSession = [
            'admin_id'   => 1,
            'admin_name' => 'Admin Utama',
            'is_admin'   => true,
        ];

        $product = DB::connection('mysql')->table('products')
            ->where('product_title', 'Produk Uji Integrasi Log')
            ->first();

        if (!$product) {
            $cat = DB::connection('mysql')->table('categories')->first();
            $id = DB::connection('mysql')->table('products')->insertGetId([
                'product_title' => 'Produk Uji Integrasi Log',
                'product_price' => 150000,
                'product_cat'   => $cat ? $cat->cat_id : 1,
            ]);
            $productId = $id;
        } else {
            $productId = $product->product_id;
        }

        $updateData = [
            'product_title'    => 'Produk Uji Integrasi Log (Updated)',
            'product_brand'    => 1,
            'product_price'    => 175000,
            'product_cat'      => $product->product_cat ?? 1,
            'product_desc'     => 'Deskripsi setelah diedit',
            'product_keywords' => 'uji,updated',
        ];

        $response = $this->withSession($adminSession)
            ->put(route('admin.products.update', $productId), $updateData);

        $response->assertRedirect(route('admin.products'));
        $response->assertSessionHas('success');

        // Verifikasi produk di DB utama terupdate
        $updatedProduct = DB::connection('mysql')->table('products')
            ->where('product_id', $productId)
            ->first();

        $this->assertEquals('Produk Uji Integrasi Log (Updated)', $updatedProduct->product_title);
        $this->assertEquals(175000, $updatedProduct->product_price);

        // Verifikasi log tercatat di ecommerce_logs
        $log = ActivityLog::where('subject_type', 'Product')
            ->where('subject_id', $productId)
            ->where('activity_type', 'Mengubah Produk')
            ->latest('id')
            ->first();

        $this->assertNotNull($log, 'Log aktivitas pengubahan produk harus tercatat.');
        $this->assertEquals(1, $log->admin_id);
        $this->assertEquals('Admin Utama', $log->admin_name);
        $this->assertNotNull($log->old_values);
        $this->assertNotNull($log->new_values);
    }

    /**
     * Skenario 3 & 5 & 6: Admin berhasil menghapus produk dan aktivitas tercatat.
     */
    public function test_admin_can_delete_product_and_activity_is_logged(): void
    {
        $adminSession = [
            'admin_id'   => 1,
            'admin_name' => 'Admin Utama',
            'is_admin'   => true,
        ];

        // Buat dummy produk khusus untuk dihapus
        $cat = DB::connection('mysql')->table('categories')->first();
        $targetId = DB::connection('mysql')->table('products')->insertGetId([
            'product_title' => 'Produk Khusus Uji Hapus',
            'product_brand' => 1,
            'product_price' => 50000,
            'product_cat'   => $cat ? $cat->cat_id : 1,
        ]);

        $response = $this->withSession($adminSession)
            ->delete(route('admin.products.destroy', $targetId));

        $response->assertRedirect(route('admin.products'));
        $response->assertSessionHas('success');

        // Pastikan di database utama sudah terhapus
        $deleted = DB::connection('mysql')->table('products')->where('product_id', $targetId)->first();
        $this->assertNull($deleted);

        // Pastikan riwayat hapus tersimpan di database kedua dengan data produk lama
        $log = ActivityLog::where('subject_type', 'Product')
            ->where('subject_id', $targetId)
            ->where('activity_type', 'Menghapus Produk')
            ->first();

        $this->assertNotNull($log, 'Log aktivitas penghapusan produk harus tercatat.');
        $this->assertEquals(1, $log->admin_id);
        $this->assertStringContainsString('Produk Khusus Uji Hapus', $log->description);
        $this->assertNotNull($log->old_values);

        // Bersihkan log uji
        $log->delete();

        // Bersihkan sisa produk jika ada
        DB::connection('mysql')->table('products')->where('product_title', 'like', 'Produk Uji Integrasi Log%')->delete();
        ActivityLog::where('description', 'like', '%Produk Uji Integrasi Log%')->delete();
    }

    /**
     * Skenario 4 & 5 & 6: Admin berhasil mengubah status pesanan dan aktivitas tercatat.
     */
    public function test_admin_can_update_order_status_and_activity_is_logged(): void
    {
        $adminSession = [
            'admin_id'   => 2,
            'admin_name' => 'Admin Manajer Pesanan',
            'is_admin'   => true,
        ];

        // Ambil atau buat pesanan untuk diuji
        $order = DB::connection('mysql')->table('orders')->first();

        if (!$order) {
            $orderId = DB::connection('mysql')->table('orders')->insertGetId([
                'user_id'    => 1,
                'product_id' => 1,
                'qty'        => 1,
                'trx_id'     => 'TRX-TEST-' . time(),
                'p_status'   => 'Pending',
            ]);
        } else {
            $orderId = $order->order_id;
        }

        $response = $this->withSession($adminSession)
            ->put(route('admin.orders.update', $orderId), [
                'p_status' => 'Completed',
            ]);

        $response->assertRedirect(route('admin.orders'));
        $response->assertSessionHas('success');

        // Pastikan status di database utama terupdate
        $updatedOrder = DB::connection('mysql')->table('orders')
            ->where('order_id', $orderId)
            ->first();

        $this->assertEquals('Completed', $updatedOrder->p_status);

        // Pastikan aktivitas tercatat di database kedua
        $log = ActivityLog::where('subject_type', 'Order')
            ->where('subject_id', $orderId)
            ->where('activity_type', 'Mengubah Status Pesanan')
            ->latest('id')
            ->first();

        $this->assertNotNull($log, 'Log perubahan status pesanan harus tercatat.');
        $this->assertEquals(2, $log->admin_id);
        $this->assertEquals('Admin Manajer Pesanan', $log->admin_name);
        $this->assertStringContainsString('Completed', $log->description);

        // Bersihkan log uji pesanan
        $log->delete();
    }

    /**
     * Skenario 7: Operasi yang gagal TIDAK menghasilkan catatan aktivitas keberhasilan.
     */
    public function test_failed_operation_does_not_produce_activity_log(): void
    {
        $adminSession = [
            'admin_id'   => 1,
            'admin_name' => 'Admin Utama',
            'is_admin'   => true,
        ];

        $initialLogCount = ActivityLog::count();

        // Coba kirim data produk yang tidak valid (contoh: product_title kosong dan harga tidak valid)
        $response = $this->withSession($adminSession)
            ->post(route('admin.products.store'), [
                'product_title' => '',
                'product_price' => 'harga_salah',
                'product_cat'   => '',
            ]);

        // Harus validasi gagal (redirect back with errors)
        $response->assertSessionHasErrors(['product_title', 'product_price', 'product_cat']);

        // Pastikan jumlah log tidak bertambah
        $currentLogCount = ActivityLog::count();
        $this->assertEquals($initialLogCount, $currentLogCount, 'Operasi gagal tidak boleh mencatat aktivitas ke tabel logs.');
    }
}
