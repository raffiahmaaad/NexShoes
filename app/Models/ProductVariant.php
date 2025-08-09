<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'stock',
        'attributes',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'attributes' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'formatted_price',
        'is_in_stock',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'variant_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->product->name . ' - ' . $this->name;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeAvailable($query)
    {
        return $query->active()->inStock();
    }

    public function scopeOrderedBySortOrder($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Methods
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    public function hasVariantAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function getVariantAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    public function getImageUrl(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        // Fallback to product image if variant has no image
        return $this->product->getImageUrl();
    }

    // Events
    protected static function booted(): void
    {
        static::creating(function (ProductVariant $variant) {
            if (empty($variant->sku)) {
                $variant->sku = $variant->generateSku();
            }
        });

        static::saved(function (ProductVariant $variant) {
            // Update product's total stock
            $variant->product->updateTotalStock();
        });

        static::deleted(function (ProductVariant $variant) {
            // Update product's total stock after variant deletion
            $variant->product->updateTotalStock();
        });
    }

    private function generateSku(): string
    {
        $productSku = $this->product->sku ?? 'PROD';
        $variantId = $this->id ?? rand(1000, 9999);

        return $productSku . '-VAR-' . $variantId;
    }
}
