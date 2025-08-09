<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'price',
        'qty',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_price',
        'formatted_subtotal',
        'total_price',
    ];

    // Relationships
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->price * $this->qty;
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->product->name;

        if ($this->variant) {
            $name .= ' - ' . $this->variant->name;
        }

        return $name;
    }

    // Methods
    public function updateQuantity(int $quantity): void
    {
        $this->qty = $quantity;
        $this->save();
    }

    public function increaseQuantity(int $quantity = 1): void
    {
        $this->qty += $quantity;
        $this->save();
    }

    public function decreaseQuantity(int $quantity = 1): void
    {
        $newQty = max(1, $this->qty - $quantity);
        $this->qty = $newQty;
        $this->save();
    }

    public function getAvailableStock(): int
    {
        if ($this->variant) {
            return $this->variant->stock;
        }

        return $this->product->stock ?? 0;
    }

    public function canIncreaseQuantity(int $quantity = 1): bool
    {
        $availableStock = $this->getAvailableStock();
        return ($this->qty + $quantity) <= $availableStock;
    }

    // Events
    protected static function booted(): void
    {
        static::saving(function (CartItem $cartItem) {
            $cartItem->subtotal = $cartItem->price * $cartItem->qty;
        });

        static::saved(function (CartItem $cartItem) {
            $cartItem->cart->calculateTotals();
        });

        static::deleted(function (CartItem $cartItem) {
            $cartItem->cart->calculateTotals();
        });
    }
}
