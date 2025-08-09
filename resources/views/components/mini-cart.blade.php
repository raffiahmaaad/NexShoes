<?php

use App\Services\CartService;
use function Livewire\Volt\{state, computed, on};

state([
    'isOpen' => false,
]);

$cart = computed(function (CartService $cartService) {
    return $cartService->getOrCreateCart()->load([
        'items.product' => function ($query) {
            $query->select('id', 'name', 'slug', 'price', 'sale_price', 'on_sale');
        },
    ]);
});

$cartCount = computed(function () {
    return $this->cart->total_items;
});

$toggleCart = function () {
    $this->isOpen = !$this->isOpen;
};

$closeCart = function () {
    $this->isOpen = false;
};

// Listen for cart updates from other components
on([
    'cart-updated' => function ($count) {
        // Refresh cart data when updated
        unset($this->cart);
    },
]);

?>

@volt
    <div class="relative" x-data="{ isOpen: @entangle('isOpen') }" @click.away="isOpen = false">
        <!-- Cart Icon Button -->
        <button @click="isOpen = !isOpen"
            class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition-colors"
            aria-label="Shopping Cart">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2"></path>
            </svg>

            <!-- Cart Badge -->
            @if ($this->cartCount > 0)
                <span
                    class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium animate-pulse">
                    {{ $this->cartCount }}
                </span>
            @endif
        </button>

        <!-- Cart Dropdown -->
        <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95 translate-y-1"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-95 translate-y-1"
            class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50"
            x-cloak>

            <!-- Cart Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Shopping Cart</h3>
                    <button @click="isOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                @if ($this->cartCount > 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $this->cartCount }}
                        item{{ $this->cartCount > 1 ? 's' : '' }} in cart</p>
                @endif
            </div>

            <!-- Cart Content -->
            <div class="max-h-96 overflow-y-auto">
                @if ($this->cart->items->count() > 0)
                    <div class="p-4 space-y-4">
                        @foreach ($this->cart->items->take(3) as $item)
                            <div class="flex items-center space-x-3">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg overflow-hidden">
                                        @if ($item->product->first_image)
                                            <img src="{{ Storage::url($item->product->first_image) }}"
                                                alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $item->product->name }}
                                    </h4>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Qty:
                                            {{ $item->qty }}</span>
                                        <span class="text-sm font-semibold text-orange-500 dark:text-orange-400">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($this->cart->items->count() > 3)
                            <div class="text-center py-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    +{{ $this->cart->items->count() - 3 }} more items
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Cart Total -->
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal:</span>
                            <span class="text-lg font-bold text-orange-500 dark:text-orange-400">
                                Rp{{ number_format($this->cart->subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <a href="/cart" @click="isOpen = false"
                                class="w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2">
                                    </path>
                                </svg>
                                View Cart
                            </a>
                            <a href="/checkout" @click="isOpen = false"
                                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:from-orange-600 hover:to-orange-700 transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Checkout
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Empty Cart -->
                    <div class="p-8 text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Start shopping to add items to your cart
                        </p>
                        <a href="/products" @click="isOpen = false"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-sm font-semibold rounded-lg hover:from-orange-600 hover:to-orange-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Start Shopping
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endvolt
