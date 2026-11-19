<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDashboardDualDatabaseTest extends TestCase
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
     * 1. Pengujian: Dashboard berhasil mengambil statistik dari database utama (ecommerce).
     * 5. Tidak ada data dummy yang digunakan.
     * 7. Seluruh komponen dashboard tetap berfungsi.
     */
    public function test_dashboard_displays_real_statistics_from_primary_database(): void
    {
        $adminSession = [
            'admin_id'   => 1,
            'admin_name' => 'Super Admin',
            'is_admin'   => true,
        ];

        $expectedProducts = DB::connection('mysql')->table('products')->count();
        $expectedUsers = DB::connection('mysql')->table('user_info')->count();
        $expectedCategories = DB::connection('mysql')->table('categories')->count();
        $expectedOrders = DB::connection('mysql')->table('orders')->count();
        $expectedCompleted = DB::connection('mysql')->table('orders')->where('p_status', 'Completed')->count();
        $expectedPending = DB::connection('mysql')->table('orders')->where('p_status', 'Pending')->count();

        $response = $this->withSession($adminSession)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalProducts', $expectedProducts);
        $response->assertViewHas('totalUsers', $expectedUsers);
        $response->assertViewHas('totalCategories', $expectedCategories);
        $response->assertViewHas('totalOrders', $expectedOrders);
        $response->assertViewHas('completedOrders', $expectedCompleted);
        $response->assertViewHas('pendingOrders', $expectedPending);

        // Memastikan HTML menampilkan data nyata
        $response->assertSee((string) $expectedProducts);
        $response->assertSee((string) $expectedUsers);
        $response->assertSee((string) $expectedOrders);
        $response->assertSee((string) $expectedCategories);
    }

    /**
     * 2. Pengujian: Dashboard berhasil mengambil aktivitas dari database kedua (ecommerce_logs).
     * 3. Aktivitas terbaru tampil pada urutan yang benar (descending).
     */
    public function test_dashboard_fetches_activity_logs_from_secondary_database_in_correct_order(): void
    {
        $adminSession = [
            'admin_id'   => 1,
            'admin_name' => 'Super Admin',
            'is_admin'   => true,
        ];

        // Buat dua catatan aktivitas di database kedua dengan timestamp berbeda
        $log1 = ActivityLog::record(
            adminId: 1,
            adminName: 'Super Admin',
            activityType: 'Menambahkan Produk',
            description: 'Aktivitas pertama diuji',
            objectInfo: 'Produk #101'
        );

        sleep(1); // Jeda 1 detik agar timestamp berbeda

        $log2 = ActivityLog::record(
            adminId: 1,
            adminName: 'Super Admin',
            activityType: 'Mengubah Status Pesanan',
            description: 'Aktivitas kedua diuji',
            objectInfo: 'Pesanan #502'
        );

        $response = $this->withSession($adminSession)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('activityLogs');

        $viewLogs = $response->viewData('activityLogs');
        $this->assertNotEmpty($viewLogs);

        // Log2 yang dibuat terakhir harus berada paling atas
        $firstItem = $viewLogs->first();
        $this->assertEquals($log2->id, $firstItem->id);
        $this->assertEquals('Mengubah Status Pesanan', $firstItem->activity_type);

        $response->assertSee('Aktivitas kedua diuji');
        $response->assertSee('Pesanan #502');

        // Bersihkan data log uji
        $log1->delete();
        $log2->delete();
    }

    /**
     * 4. Pengujian: Data dashboard berubah dinamis setelah admin melakukan tindakan.
     */
    public function test_dashboard_reflects_changes_after_admin_action(): void
    {
        $adminSession = [
            'admin_id'   => 1,
            'admin_name' => 'Super Admin',
            'is_admin'   => true,
        ];

        $initialProductCount = DB::connection('mysql')->table('products')->count();

        // 4a. Admin menambahkan produk baru
        $cat = DB::connection('mysql')->table('categories')->first();
        $newProductTitle = 'Laptop Dinamis Dashboard Test ' . time();

        $this->withSession($adminSession)->post(route('admin.products.store'), [
            'product_title' => $newProductTitle,
            'product_brand' => 1,
            'product_price' => 12500000,
            'product_cat'   => $cat ? $cat->cat_id : 1,
        ]);

        // Cek dashboard lagi: total produk harus bertambah +1 dan aktivitas penambahan produk tercatat di tabel
        $response = $this->withSession($adminSession)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalProducts', $initialProductCount + 1);
        $response->assertSee($newProductTitle);
        $response->assertSee('Menambahkan Produk');

        // Bersihkan produk dan log uji
        DB::connection('mysql')->table('products')->where('product_title', $newProductTitle)->delete();
        ActivityLog::where('description', 'like', "%{$newProductTitle}%")->delete();
    }

    /**
     * 6. Pengujian: Tidak terjadi kesalahan (graceful degradation) ketika database kedua mengalami gangguan.
     */
    public function test_dashboard_still_works_gracefully_when_secondary_database_fails(): void
    {
        $adminSession = [
            'admin_id'   => 1,
            'admin_name' => 'Super Admin',
            'is_admin'   => true,
        ];

        // Simulasikan database kedua gagal / down
        Config::set('database.connections.mysql_logs.port', '9999');
        DB::purge('mysql_logs');

        // Akses dashboard: harus tetap HTTP 200 (tidak crash / 500)
        $response = $this->withSession($adminSession)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('logsError');
        $response->assertSee('Koneksi ke database log aktivitas sedang mengalami gangguan');

        // Statistik database utama tetap sukses ditampilkan
        $response->assertViewHas('totalProducts');

        // Pulihkan koneksi mysql_logs
        DB::purge('mysql_logs');
    }
}
