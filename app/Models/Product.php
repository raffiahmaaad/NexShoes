<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'image',
        'description',
        'price',
        'is_active',
        'is_featured',
        'in_stock',
        'stock_quantity',
        'on_sale',
        'sale_price'
    ];

    protected $casts = [
        'image' => 'array', // Cast JSON to array
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'in_stock' => 'boolean',
        'on_sale' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
        'in_stock' => true,
        'stock_quantity' => 0,
        'on_sale' => false,
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the first image from the images array
     */
    public function getFirstImageAttribute(): ?string
    {
        if (!$this->image || !is_array($this->image) || empty($this->image)) {
            return null;
        }
        return $this->image[0] ?? null;
    }

    /**
     * Get all images as array
     */
    public function getImagesAttribute(): array
    {
        return $this->image ?: [];
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Check if product has images
     */
    public function getHasImagesAttribute(): bool
    {
        return !empty($this->image) && is_array($this->image) && count($this->image) > 0;
    }

    /**
     * Scope for active products
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for in stock products
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('in_stock', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for products on sale
     */
    public function scopeOnSale(Builder $query): Builder
    {
        return $query->where('on_sale', true);
    }

    /**
     * Scope for products by category
     */
    public function scopeByCategory(Builder $query, $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for products by brand
     */
    public function scopeByBrand(Builder $query, $brandId): Builder
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope for price range
     */
    public function scopePriceRange(Builder $query, $min = null, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope for search by name or description
     */
    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        // Update slug when name changes
        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
