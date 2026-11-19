<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->table('products', function (Blueprint $table) {
            if (!Schema::connection('mysql')->hasColumn('products', 'stock')) {
                $table->integer('stock')->default(10)->after('product_price');
            }
        });

        // Setel seluruh produk yang sudah ada agar memiliki stok tepat 10 unit
        DB::connection('mysql')->table('products')->update(['stock' => 10]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('products', function (Blueprint $table) {
            if (Schema::connection('mysql')->hasColumn('products', 'stock')) {
                $table->dropColumn('stock');
            }
        });
    }
};
