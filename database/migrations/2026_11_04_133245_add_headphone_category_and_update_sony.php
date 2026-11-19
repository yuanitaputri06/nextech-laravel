<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah kategori "Headphone" dan pindahkan produk Sony
     * yang sebelumnya di kategori Aksesori ke kategori Headphone.
     */
    public function up(): void
    {
        // 1. Cek apakah kategori "Headphone" sudah ada
        $existing = DB::table('categories')
            ->whereRaw('LOWER(cat_title) LIKE ?', ['%headphone%'])
            ->first();

        if ($existing) {
            $headphoneCatId = $existing->cat_id;
        } else {
            // 2. Tambah kategori baru "Headphone"
            $headphoneCatId = DB::table('categories')->insertGetId([
                'cat_title' => 'Headphone',
            ]);
        }

        // 3. Cari cat_id dari kategori Aksesori (berbagai ejaan)
        $aksesoriCat = DB::table('categories')
            ->whereRaw('LOWER(cat_title) LIKE ?', ['%aksesori%'])
            ->orWhereRaw('LOWER(cat_title) LIKE ?', ['%accessories%'])
            ->first();

        // 4. Update produk Sony:
        //    - yang judulnya mengandung "sony", DAN
        //    - yang kategorinya Aksesori (jika ditemukan) ATAU semua produk Sony
        $sonyQuery = DB::table('products')
            ->whereRaw('LOWER(product_title) LIKE ?', ['%sony%']);

        if ($aksesoriCat) {
            // Hanya produk Sony yang ada di Aksesori
            $sonyQuery->where('product_cat', $aksesoriCat->cat_id);
        }

        $updated = $sonyQuery->update(['product_cat' => $headphoneCatId]);

        // Log hasil
        \Illuminate\Support\Facades\Log::info(
            "Migration: Kategori Headphone (ID: {$headphoneCatId}) ditambahkan. " .
            "{$updated} produk Sony dipindahkan ke kategori Headphone."
        );
    }

    /**
     * Rollback: kembalikan produk Sony ke Aksesori & hapus kategori Headphone
     * (hanya jika kategori Headphone dibuat oleh migration ini).
     */
    public function down(): void
    {
        $headphoneCat = DB::table('categories')
            ->where('cat_title', 'Headphone')
            ->first();

        $aksesoriCat = DB::table('categories')
            ->whereRaw('LOWER(cat_title) LIKE ?', ['%aksesori%'])
            ->orWhereRaw('LOWER(cat_title) LIKE ?', ['%accessories%'])
            ->first();

        if ($headphoneCat && $aksesoriCat) {
            // Kembalikan produk Sony ke Aksesori
            DB::table('products')
                ->where('product_cat', $headphoneCat->cat_id)
                ->whereRaw('LOWER(product_title) LIKE ?', ['%sony%'])
                ->update(['product_cat' => $aksesoriCat->cat_id]);
        }

        if ($headphoneCat) {
            // Hapus kategori Headphone jika tidak ada produk tersisa
            $remaining = DB::table('products')
                ->where('product_cat', $headphoneCat->cat_id)
                ->count();

            if ($remaining === 0) {
                DB::table('categories')->where('cat_id', $headphoneCat->cat_id)->delete();
            }
        }
    }
};
