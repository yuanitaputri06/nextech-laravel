<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_histories';

    protected $fillable = [
        'trx_id',
        'order_id',
        'status',
        'title',
        'description',
        'actor_type',
        'actor_id',
        'actor_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Catat riwayat perubahan status pesanan
     */
    public static function record(
        string $trxId,
        string $status,
        string $title,
        ?string $description = null,
        string $actorType = 'system',
        ?int $actorId = null,
        ?string $actorName = null,
        ?int $orderId = null
    ): self {
        return self::create([
            'trx_id' => $trxId,
            'order_id' => $orderId,
            'status' => $status,
            'title' => $title,
            'description' => $description,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
        ]);
    }

    /**
     * Relasi ke Order jika menggunakan order_id
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
