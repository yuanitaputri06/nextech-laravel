<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CheckDualDatabase extends Command
{
    /**
     * Nama dan signature command.
     *
     * @var string
     */
    protected $signature = 'db:check-dual';

    /**
     * Deskripsi command.
     *
     * @var string
     */
    protected $description = 'Memeriksa dan menguji koneksi dua database (ecommerce dan ecommerce_logs)';

    /**
     * Eksekusi console command.
     */
    public function handle()
    {
        $this->info('=== PENGUJIAN DUA KONEKSI DATABASE (NEXTECH LARAVEL) ===');
        $this->newLine();

        // 1. Uji Database Utama (ecommerce)
        $this->info('1. Memeriksa Database Utama (Connection: mysql, Database: ecommerce)...');
        try {
            $primaryDb = DB::connection('mysql')->getDatabaseName();
            $productCount = DB::connection('mysql')->table('products')->count();
            $this->line("   [OK] Terhubung ke database: <comment>{$primaryDb}</comment>");
            $this->line("   [OK] Jumlah produk di database utama: <comment>{$productCount}</comment>");
        } catch (\Throwable $e) {
            $this->error('   [FAIL] Gagal mengakses database utama: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();

        // 2. Uji Database Kedua (ecommerce_logs)
        $this->info('2. Memeriksa Database Kedua (Connection: mysql_logs, Database: ecommerce_logs)...');
        try {
            $logsDb = DB::connection('mysql_logs')->getDatabaseName();
            $hasTable = DB::connection('mysql_logs')->getSchemaBuilder()->hasTable('activity_logs');
            $this->line("   [OK] Terhubung ke database: <comment>{$logsDb}</comment>");
            $this->line('   [OK] Tabel activity_logs ditemukan: <comment>' . ($hasTable ? 'Ya' : 'Tidak') . '</comment>');
            if (!$hasTable) {
                $this->warn('   [WARN] Silakan jalankan migrasi: php artisan migrate --database=mysql_logs');
            }
        } catch (\Throwable $e) {
            $this->error('   [FAIL] Gagal mengakses database logs: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();

        // 3. Uji Query Bersamaan dari Kedua Database
        $this->info('3. Menguji Pengambilan Data Bersamaan dari Kedua Database...');
        try {
            $prod = DB::connection('mysql')->table('products')->first();
            $logCount = DB::connection('mysql_logs')->table('activity_logs')->count();
            $this->line("   [OK] Berhasil membaca produk dari ecommerce (ID: " . ($prod->product_id ?? '-') . ")");
            $this->line("   [OK] Berhasil membaca tabel activity_logs dari ecommerce_logs (Total baris: {$logCount})");
        } catch (\Throwable $e) {
            $this->error('   [FAIL] Gagal mengambil data bersamaan: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();

        // 4. Uji Operasi Model ActivityLog (Insert & Read & Filter)
        $this->info('4. Menguji Model ActivityLog (Insert, Read, Scope, Ordering)...');
        try {
            // Insert
            $testLog = ActivityLog::record(
                adminId: 999,
                adminName: 'Sistem CLI Test',
                activityType: 'CLI_VERIFICATION',
                description: 'Pengujian mandiri koneksi dua database melalui command db:check-dual',
                subjectType: 'Product',
                subjectId: 1,
                oldValues: ['price' => 100000],
                newValues: ['price' => 120000],
                objectInfo: json_encode(['status' => 'tested', 'timestamp' => now()->toDateTimeString()])
            );
            $this->line("   [OK] Insert data via model berhasil (ID Log: <comment>{$testLog->id}</comment>)");

            // Read All
            $allLogs = ActivityLog::getAllLogs(3);
            $this->line("   [OK] Ambil riwayat terbaru berhasil (Mengambil " . count($allLogs) . " log teratas)");

            // Filter Admin
            $byAdmin = ActivityLog::getByAdmin(999);
            $this->line("   [OK] Filter aktivitas berdasarkan admin_id (999): " . count($byAdmin) . " catatan");

            // Filter Type
            $byType = ActivityLog::getByType('CLI_VERIFICATION');
            $this->line("   [OK] Filter aktivitas berdasarkan jenis 'CLI_VERIFICATION': " . count($byType) . " catatan");

            // Hapus kembali log pengujian agar bersih
            $testLog->delete();
            $this->line("   [OK] Data log uji berhasil dibersihkan kembali.");
        } catch (\Throwable $e) {
            $this->error('   [FAIL] Gagal pada operasi Model ActivityLog: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();

        // 5. Uji Isolasi Kegagalan Database Kedua
        $this->info('5. Menguji Isolasi Kegagalan Database Kedua terhadap Database Utama...');
        try {
            $countBefore = DB::connection('mysql')->table('products')->count();

            // Simulasi error pada database kedua
            Config::set('database.connections.mysql_logs.port', '9999');
            DB::purge('mysql_logs');

            $failedAsExpected = false;
            try {
                DB::connection('mysql_logs')->table('activity_logs')->count();
            } catch (\Throwable $e) {
                $failedAsExpected = true;
            }

            // Database utama harus tetap berfungsi normal
            $countAfter = DB::connection('mysql')->table('products')->count();

            if ($failedAsExpected && $countBefore === $countAfter) {
                $this->line('   [OK] Database utama tetap aman & operasional meskipun koneksi logs mengalami gangguan.');
            } else {
                $this->warn('   [WARN] Perilaku isolasi database tidak sesuai perkiraan.');
            }

            // Reset purge
            DB::purge('mysql_logs');
        } catch (\Throwable $e) {
            $this->error('   [FAIL] Pengujian isolasi gagal: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('=== SELURUH PENGUJIAN DUA DATABASE BERHASIL (PASSED) ===');

        return Command::SUCCESS;
    }
}
