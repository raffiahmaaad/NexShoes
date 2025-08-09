<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Size L", "Color Red", "Size M - Blue"
            $table->string('sku')->unique()->nullable(); // Unique SKU for this variant
            $table->decimal('price', 15, 2); // Variant price (can override product base price)
            $table->integer('stock')->default(0); // Stock for this specific variant
            $table->json('attributes')->nullable(); // Store variant attributes like {"size": "L", "color": "Red"}
            $table->string('image')->nullable(); // Optional variant-specific image
            $table->boolean('is_active')->default(true); // Enable/disable variant
            $table->integer('sort_order')->default(0); // For ordering variants
            $table->timestamps();

            // Indexes for better performance
            $table->index(['product_id', 'is_active']);
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
