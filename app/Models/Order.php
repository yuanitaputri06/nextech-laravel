<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'user_id',
        'product_id',
        'qty',
        'trx_id',
        'p_status',
        'order_status',
        'payment_method',
        'total_amt',
        'snap_token',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_status',
        'paid_at',
        'courier_name',
        'tracking_number',
        'shipping_notes',
        'processed_at',
        'shipped_at',
        'completed_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'processed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_amt' => 'integer',
        'qty' => 'integer',
    ];

    /**
     * Label representatif untuk status pembayaran
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        $status = strtolower($this->p_status ?? 'pending');
        return match ($status) {
            'completed', 'selesai', 'paid' => 'Terbayar',
            'cancelled', 'batal' => 'Dibatalkan',
            default => 'Menunggu Pembayaran',
        };
    }

    /**
     * Badge CSS class untuk status pembayaran
     */
    public function getPaymentStatusBadgeClassAttribute(): string
    {
        $status = strtolower($this->p_status ?? 'pending');
        return match ($status) {
            'completed', 'selesai', 'paid' => 'status-completed',
            'cancelled', 'batal' => 'status-cancelled',
            default => 'status-pending',
        };
    }

    /**
     * Label representatif untuk status pemesanan
     */
    public function getOrderStatusLabelAttribute(): string
    {
        return match ($this->order_status) {
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'pembayaran_berhasil' => 'Menunggu Diproses',
            'sedang_diproses' => 'Sedang Diproses',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->order_status ?? 'Menunggu Pembayaran')),
        };
    }

    /**
     * Badge CSS class untuk status pesanan
     */
    public function getOrderStatusBadgeClassAttribute(): string
    {
        return match ($this->order_status) {
            'menunggu_pembayaran' => 'status-pending',
            'pembayaran_berhasil' => 'status-info',
            'sedang_diproses' => 'status-process',
            'dikirim' => 'status-shipping',
            'selesai' => 'status-completed',
            'dibatalkan' => 'status-cancelled',
            default => 'status-default',
        };
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Relasi ke Riwayat Perubahan Status (berdasarkan trx_id)
     */
    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'trx_id', 'trx_id')->orderBy('id', 'asc');
    }
}
