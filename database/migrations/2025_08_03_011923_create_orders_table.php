<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('number')->unique();
            $table->enum('status', ['pending', 'paid', 'failed', 'canceled', 'shipped', 'completed'])->default('pending');
            $table->string('currency', 3)->default('IDR');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('shipping_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('shipping_method')->nullable();

            // Shipping address
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->text('shipping_street');
            $table->string('shipping_city');
            $table->string('shipping_province');
            $table->string('shipping_postal_code');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
