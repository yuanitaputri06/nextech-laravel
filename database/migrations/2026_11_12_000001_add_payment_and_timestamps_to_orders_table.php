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
        Schema::connection('mysql')->table('orders', function (Blueprint $table) {
            if (!Schema::connection('mysql')->hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->default('Cash')->after('p_status');
            }
            if (!Schema::connection('mysql')->hasColumn('orders', 'total_amt')) {
                $table->integer('total_amt')->nullable()->default(0)->after('payment_method');
            }
            if (!Schema::connection('mysql')->hasColumn('orders', 'created_at')) {
                $table->timestamp('created_at')->nullable()->useCurrent()->after('total_amt');
            }
            if (!Schema::connection('mysql')->hasColumn('orders', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->after('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('orders', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::connection('mysql')->hasColumn('orders', 'payment_method') ? 'payment_method' : null,
                Schema::connection('mysql')->hasColumn('orders', 'total_amt') ? 'total_amt' : null,
                Schema::connection('mysql')->hasColumn('orders', 'created_at') ? 'created_at' : null,
                Schema::connection('mysql')->hasColumn('orders', 'updated_at') ? 'updated_at' : null,
            ]));
        });
    }
};
