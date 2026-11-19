<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ProductStockDisplayTest extends TestCase
{
    /**
     * Pastikan halaman katalog tidak menampilkan informasi stok.
     */
    public function test_catalog_does_not_display_stock_information(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee('Stok Terbatas');
        $response->assertDontSee('Stok Tersedia');
        $response->assertDontSee('Stok Habis');
        $response->assertDontSee('product-stock-tag');
    }

    /**
     * Pastikan halaman beranda (produk terbaru) tidak menampilkan informasi stok.
     */
    public function test_home_page_does_not_display_stock_information(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Stok Terbatas');
        $response->assertDontSee('Stok Tersedia');
        $response->assertDontSee('Stok Habis');
        $response->assertDontSee('product-stock-tag');
    }

    /**
     * Pastikan halaman detail produk menampilkan stok aktual dan TIDAK menampilkan label "Stok Terbatas".
     */
    public function test_product_detail_displays_actual_stock_count_and_not_stock_terbatas(): void
    {
        $product = DB::table('products')->first();

        if (!$product) {
            $this->markTestSkipped('Tidak ada produk di database.');
        }

        $response = $this->get('/product/' . $product->product_id);

        $response->assertStatus(200);

        if ((int) $product->stock > 0) {
            $response->assertSee("Stok Tersedia: {$product->stock} Unit");
        } else {
            $response->assertSee('Stok Habis');
        }

        $response->assertDontSee('Stok Terbatas');
    }

    /**
     * Pastikan endpoint validasi stok backend menerima jumlah yang valid.
     */
    public function test_backend_stock_validation_endpoint_accepts_valid_quantity(): void
    {
        $product = DB::table('products')->where('stock', '>', 0)->first();

        if (!$product) {
            $this->markTestSkipped('Tidak ada produk berstok di database.');
        }

        $response = $this->postJson("/product/{$product->product_id}/check-stock", [
            'qty' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'available' => true,
            'stock' => (int) $product->stock,
        ]);
    }

    /**
     * Pastikan endpoint validasi stok backend menolak jumlah yang melebihi stok.
     */
    public function test_backend_stock_validation_endpoint_rejects_quantity_exceeding_stock(): void
    {
        $product = DB::table('products')->first();

        if (!$product) {
            $this->markTestSkipped('Tidak ada produk di database.');
        }

        $excessQty = (int) $product->stock + 100;

        $response = $this->postJson("/product/{$product->product_id}/check-stock", [
            'qty' => $excessQty,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
        ]);
    }
}
