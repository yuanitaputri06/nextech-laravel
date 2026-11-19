<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /*
    |--------------------------------------------------------------------------
    | NAMA TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'products';


    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'product_id';


    /*
    |--------------------------------------------------------------------------
    | TIMESTAMPS
    |--------------------------------------------------------------------------
    |
    | Tabel products milik database lama tidak menggunakan
    | created_at dan updated_at.
    |
    */

    public $timestamps = false;


    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    |
    | Kolom yang diperbolehkan untuk diisi melalui create()
    | atau update().
    |
    */

    protected $fillable = [

        'product_cat',

        'product_brand',

        'product_title',

        'product_price',

        'stock',

        'product_desc',

        'product_image',

        'product_keywords',

    ];


    /*
    |--------------------------------------------------------------------------
    | CAST DATA
    |--------------------------------------------------------------------------
    |
    | Harga produk akan diperlakukan sebagai angka.
    |
    */

    protected $casts = [

        'product_id' => 'integer',

        'product_cat' => 'integer',

        'product_brand' => 'integer',

        'product_price' => 'integer',

        'stock' => 'integer',

    ];

    /**
     * Accessor untuk status ketersediaan stok produk.
     * - Lebih dari 10: Tersedia
     * - 1 sampai 10: Stok Terbatas
     * - 0 atau kurang: Habis
     */
    public function getStockStatusAttribute(): string
    {
        $currentStock = (int) ($this->stock ?? 0);

        if ($currentStock > 10) {
            return 'Tersedia';
        } elseif ($currentStock >= 1) {
            return 'Stok Terbatas';
        } else {
            return 'Habis';
        }
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'product_id', 'product_id');
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : 0;
    }

    public function getReviewCountAttribute(): int
    {
        return (int) $this->reviews()->count();
    }
}