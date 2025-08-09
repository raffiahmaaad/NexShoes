<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'status',
        'currency',
        'subtotal',
        'discount_total',
        'shipping_total',
        'grand_total',
        'payment_method',
        'payment_reference',
        'shipping_method',
        'shipping_name',
        'shipping_phone',
        'shipping_street',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'NS-';
        $timestamp = now()->format('ymd');
        $count = self::whereDate('created_at', now())->count() + 1;

        return $prefix . $timestamp . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span>',
            'paid' => '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Paid</span>',
            'shipped' => '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Shipped</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Completed</span>',
            'failed' => '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Failed</span>',
            'canceled' => '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Canceled</span>',
            default => '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Unknown</span>',
        };
    }
}
