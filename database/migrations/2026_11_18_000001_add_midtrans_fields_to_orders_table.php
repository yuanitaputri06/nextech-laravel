<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::connection('mysql')->hasTable('orders') ? 'mysql' : null;

        Schema::connection($connection)->table('orders', function (Blueprint $table) use ($connection) {
            if (!Schema::connection($connection)->hasColumn('orders', 'snap_token')) {
                $table->string('snap_token', 255)->nullable()->after('payment_method');
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'midtrans_transaction_id')) {
                $table->string('midtrans_transaction_id', 100)->nullable()->after('snap_token');
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'midtrans_payment_type')) {
                $table->string('midtrans_payment_type', 50)->nullable()->after('midtrans_transaction_id');
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'midtrans_status')) {
                $table->string('midtrans_status', 50)->nullable()->after('midtrans_payment_type');
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('midtrans_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::connection('mysql')->hasTable('orders') ? 'mysql' : null;

        Schema::connection($connection)->table('orders', function (Blueprint $table) use ($connection) {
            $colsToDrop = array_filter([
                Schema::connection($connection)->hasColumn('orders', 'snap_token') ? 'snap_token' : null,
                Schema::connection($connection)->hasColumn('orders', 'midtrans_transaction_id') ? 'midtrans_transaction_id' : null,
                Schema::connection($connection)->hasColumn('orders', 'midtrans_payment_type') ? 'midtrans_payment_type' : null,
                Schema::connection($connection)->hasColumn('orders', 'midtrans_status') ? 'midtrans_status' : null,
                Schema::connection($connection)->hasColumn('orders', 'paid_at') ? 'paid_at' : null,
            ]);

            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
