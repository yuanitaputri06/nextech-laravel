<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Koneksi database yang digunakan migration ini.
     * Mengarah ke koneksi kedua: mysql_logs (database: ecommerce_logs).
     *
     * @var string
     */
    protected $connection = 'mysql_logs';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::connection('mysql_logs')->hasTable('activity_logs')) {
            Schema::connection('mysql_logs')->create('activity_logs', function (Blueprint $table) {
                $table->id(); // ID aktivitas (Primary Key)
                $table->unsignedBigInteger('admin_id')->nullable(); // ID admin yang melakukan aktivitas
                $table->string('admin_name')->nullable(); // Nama atau identitas admin
                $table->string('activity_type', 100); // Jenis aktivitas (misal: CREATE_PRODUCT, UPDATE_ORDER, LOGIN, dll.)
                $table->text('description'); // Deskripsi aktivitas
                $table->text('object_info')->nullable(); // Informasi objek yang mengalami perubahan
                $table->timestamps(); // Waktu aktivitas (created_at & updated_at)
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_logs')->dropIfExists('activity_logs');
    }
};
