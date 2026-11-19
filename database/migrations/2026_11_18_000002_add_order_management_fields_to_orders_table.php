<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::connection('mysql')->hasTable('orders') ? 'mysql' : null;

        // 1. Tambah field manajemen pesanan ke tabel orders
        Schema::connection($connection)->table('orders', function (Blueprint $table) use ($connection) {
            if (!Schema::connection($connection)->hasColumn('orders', 'order_status')) {
                $table->string('order_status', 50)->default('menunggu_pembayaran')->after('p_status')->index();
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'courier_name')) {
                $table->string('courier_name', 100)->nullable()->after('paid_at');
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number', 100)->nullable()->after('courier_name')->index();
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'shipping_notes')) {
                $table->text('shipping_notes')->nullable()->after('tracking_number');
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('shipping_notes');
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('processed_at');
            }
            if (!Schema::connection($connection)->hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('shipped_at');
            }
        });

        // 2. Sinkronkan pesanan eksisting agar memiliki order_status yang valid
        try {
            DB::connection($connection)->table('orders')
                ->whereIn(DB::raw('LOWER(p_status)'), ['completed', 'selesai'])
                ->update([
                    'order_status' => 'selesai',
                    'completed_at' => DB::raw('COALESCE(paid_at, updated_at, NOW())'),
                ]);

            DB::connection($connection)->table('orders')
                ->whereIn(DB::raw('LOWER(p_status)'), ['cancelled', 'batal'])
                ->update([
                    'order_status' => 'dibatalkan',
                ]);
        } catch (\Throwable $e) {
            // Ignore if in test/empty db
        }

        // 3. Buat tabel order_status_histories untuk mencatat riwayat perubahan status
        if (!Schema::connection($connection)->hasTable('order_status_histories')) {
            Schema::connection($connection)->create('order_status_histories', function (Blueprint $table) {
                $table->id();
                $table->string('trx_id', 100)->index();
                $table->unsignedBigInteger('order_id')->nullable()->index();
                $table->string('status', 50)->index();
                $table->string('title', 100);
                $table->text('description')->nullable();
                $table->string('actor_type', 50)->default('system'); // 'user', 'admin', 'system'
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('actor_name', 100)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::connection('mysql')->hasTable('orders') ? 'mysql' : null;

        if (Schema::connection($connection)->hasTable('order_status_histories')) {
            Schema::connection($connection)->dropIfExists('order_status_histories');
        }

        Schema::connection($connection)->table('orders', function (Blueprint $table) use ($connection) {
            $colsToDrop = array_filter([
                Schema::connection($connection)->hasColumn('orders', 'order_status') ? 'order_status' : null,
                Schema::connection($connection)->hasColumn('orders', 'courier_name') ? 'courier_name' : null,
                Schema::connection($connection)->hasColumn('orders', 'tracking_number') ? 'tracking_number' : null,
                Schema::connection($connection)->hasColumn('orders', 'shipping_notes') ? 'shipping_notes' : null,
                Schema::connection($connection)->hasColumn('orders', 'processed_at') ? 'processed_at' : null,
                Schema::connection($connection)->hasColumn('orders', 'shipped_at') ? 'shipped_at' : null,
                Schema::connection($connection)->hasColumn('orders', 'completed_at') ? 'completed_at' : null,
            ]);

            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
