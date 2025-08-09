<?php
// File: app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'grand_total',
        'payment_method',
        'payment_status',
        'status',
        'currency',
        'shipping_amount',
        'shipping_method',
        'notes',
    ];

    protected $casts = [
        'grand_total'     => 'decimal:2',
        'shipping_amount' => 'decimal:2',
    ];

    /**
     * Relasi ke tabel users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke tabel addresses (One-to-One).
     * Setiap order memiliki satu alamat.
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    /**
     * Relasi ke tabel order_items.
     * Nama diubah dari 'orderItems' menjadi 'items' untuk memperbaiki error.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
