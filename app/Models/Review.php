<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'rating' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function getUserAttribute()
    {
        return DB::table('user_info')->where('user_id', $this->user_id)->first();
    }
}
