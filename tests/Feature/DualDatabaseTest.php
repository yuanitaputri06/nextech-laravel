<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Product;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DualDatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan koneksi mysql menggunakan database ecommerce aktual jika tertimpa testing config
        if (config('database.connections.mysql.database') === ':memory:' || config('database.connections.mysql.database') === null) {
            config(['database.connections.mysql.database' => 'ecommerce']);
            DB::purge('mysql');
        }
    }

    /**
     * 1. Pengujian: Database ecommerce dapat diakses.
     */
    public function test_primary_database_ecommerce_is_accessible(): void
    {
        $connectionName = DB::connection('mysql')->getDatabaseName();
        $this->assertEquals(config('database.connections.mysql.database'), $connectionName);

        // Pastikan tabel utama seperti products atau categories bisa di-query
        $count = DB::connection('mysql')->table('categories')->count();
        $this->assertIsInt($count);
    }

    /**
     * 2. Pengujian: Database ecommerce_logs dapat diakses.
     */
    public function test_secondary_database_ecommerce_logs_is_accessible(): void
    {
        $connectionName = DB::connection('mysql_logs')->getDatabaseName();
        $this->assertEquals(config('database.connections.mysql_logs.database'), $connectionName);

        // Pastikan tabel activity_logs ada di database ecommerce_logs
        $hasTable = DB::connection('mysql_logs')->getSchemaBuilder()->hasTable('activity_logs');
        $this->assertTrue($hasTable);
    }

    /**
     * 3. Pengujian: Laravel dapat mengambil data dari kedua database secara bersamaan.
     */
    public function test_laravel_can_fetch_from_both_databases_simultaneously(): void
    {
        $products = DB::connection('mysql')->table('products')->limit(1)->get();
        $logs = DB::connection('mysql_logs')->table('activity_logs')->limit(1)->get();

        $this->assertNotNull($products);
        $this->assertNotNull($logs);
    }

    /**
     * 4. Pengujian: Model ActivityLog dapat menyimpan dan membaca data.
     */
    public function test_activity_log_model_can_create_and_retrieve_data(): void
    {
        // 4a. Insert data
        $log = ActivityLog::record(
            adminId: 1,
            adminName: 'Admin Test',
            activityType: 'UNIT_TEST',
            description: 'Pengujian otomatis dua koneksi database',
            objectInfo: json_encode(['test_key' => 'test_value'])
        );

        $this->assertNotNull($log->id);
        $this->assertEquals('UNIT_TEST', $log->activity_type);
        $this->assertEquals('mysql_logs', $log->getConnectionName());

        // 4b. Mengambil seluruh riwayat aktivitas diurutkan terbaru
        $allLogs = ActivityLog::getAllLogs(5);
        $this->assertNotEmpty($allLogs);
        $this->assertEquals($log->id, $allLogs->first()->id);

        // 4c. Mengambil aktivitas berdasarkan admin
        $adminLogs = ActivityLog::getByAdmin(1);
        $this->assertTrue($adminLogs->contains('id', $log->id));

        // 4d. Mengambil aktivitas berdasarkan jenis aktivitas
        $typeLogs = ActivityLog::getByType('UNIT_TEST');
        $this->assertTrue($typeLogs->contains('id', $log->id));

        // Cleanup log test
        $log->delete();
    }

    /**
     * 5. Pengujian: Kegagalan koneksi database kedua tidak menyebabkan kerusakan data database utama.
     */
    public function test_secondary_database_failure_does_not_affect_primary_database(): void
    {
        // Catat jumlah produk di database utama
        $initialProductCount = DB::connection('mysql')->table('products')->count();

        // Simulasikan kegagalan koneksi database kedua dengan konfigurasi port/host yang salah
        Config::set('database.connections.mysql_logs.port', '9999');
        DB::purge('mysql_logs');

        // Coba akses database kedua, harus throw exception
        $secondaryDbFailed = false;
        try {
            DB::connection('mysql_logs')->table('activity_logs')->get();
        } catch (\Throwable $e) {
            $secondaryDbFailed = true;
        }

        $this->assertTrue($secondaryDbFailed, 'Koneksi database kedua berhasil digagalkan untuk simulasi.');

        // Pastikan database utama tetap dapat diakses normal tanpa error dan data tetap utuh
        $currentProductCount = DB::connection('mysql')->table('products')->count();
        $this->assertEquals($initialProductCount, $currentProductCount);

        // Pulihkan konfigurasi mysql_logs
        DB::purge('mysql_logs');
    }
}
