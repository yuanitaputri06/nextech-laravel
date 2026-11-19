<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'p_id',
        'ip_add',
        'user_id',
        'qty',
    ];

    protected $casts = [
        'id' => 'integer',
        'p_id' => 'integer',
        'user_id' => 'integer',
        'qty' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id', 'product_id');
    }
}
