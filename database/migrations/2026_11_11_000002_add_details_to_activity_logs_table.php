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
        if (Schema::connection('mysql_logs')->hasTable('activity_logs') && !Schema::connection('mysql_logs')->hasColumn('activity_logs', 'subject_type')) {
            Schema::connection('mysql_logs')->table('activity_logs', function (Blueprint $table) {
                $table->string('subject_type', 100)->nullable()->after('activity_type');
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
                $table->text('old_values')->nullable()->after('object_info');
                $table->text('new_values')->nullable()->after('old_values');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_logs')->table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['subject_type', 'subject_id', 'old_values', 'new_values']);
        });
    }
};
