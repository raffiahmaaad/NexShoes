<?php

use App\Services\CartService;
use App\Models\CartItem;
use function Livewire\Volt\{state, computed, mount, on};

state([
    'isLoading' => false,
]);

$cart = computed(function () {
    $cartService = app(CartService::class);
    return $cartService->getOrCreateCart(auth()->id())->load(['items.product.brand', 'items.product.category']);
});

$updateQuantity = function (int $itemId, int $quantity) {
    $cartService = app(CartService::class);
    $this->isLoading = true;

    $result = $cartService->updateQuantity(auth()->id(), $itemId, $quantity);

    if ($result['success']) {
        session()->flash('success', $result['message']);
        $this->dispatch('cart-updated', count: $this->cart->total_items);
    } else {
        session()->flash('error', $result['message']);
    }

    $this->isLoading = false;
};

$removeItem = function (int $itemId) {
    $cartService = app(CartService::class);
    $this->isLoading = true;

    $result = $cartService->removeItem(auth()->id(), $itemId);

    if ($result['success']) {
        session()->flash('success', $result['message']);
        $this->dispatch('cart-updated', count: $this->cart->total_items);
    } else {
        session()->flash('error', $result['message']);
    }

    $this->isLoading = false;
};

$clearCart = function () {
    $cartService = app(CartService::class);
    $this->isLoading = true;

    $result = $cartService->clearCart(auth()->id());

    if ($result['success']) {
        session()->flash('success', $result['message']);
        $this->dispatch('cart-updated', count: 0);
    } else {
        session()->flash('error', $result['message']);
    }

    $this->isLoading = false;
};

?>
@volt
    <div>
        <x-layouts.public-app>
            <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Shopping Cart</h1>
                                <p class="mt-2 text-gray-600 dark:text-gray-400">
                                    @if ($this->cart->items->count() > 0)
                                        {{ $this->cart->items->count() }}
                                        item{{ $this->cart->items->count() > 1 ? 's' : '' }} in your cart
                                    @else
                                        Your cart is empty
                                    @endif
                                </p>
                            </div>
                            @if ($this->cart->items->count() > 0)
                                <button wire:click="clearCart" wire:confirm="Are you sure you want to clear your cart?"
                                    class="px-4 py-2 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Clear Cart
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    @if ($this->cart->items->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Cart Items -->
                            <div class="lg:col-span-2 space-y-4">
                                @foreach ($this->cart->items as $item)
                                    <div wire:key="cart-item-{{ $item->id }}"
                                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                                        <div class="flex items-start space-x-4">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-xl overflow-hidden">
                                                    @if ($item->product->first_image)
                                                        <img src="{{ Storage::url($item->product->first_image) }}"
                                                            alt="{{ $item->product->name }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <svg class="w-8 h-8 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1"
                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Product Details -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h3
                                                            class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                            <a href="/products/{{ $item->product->slug }}"
                                                                class="hover:text-orange-500 dark:hover:text-orange-400 transition-colors">
                                                                {{ $item->product->name }}
                                                            </a>
                                                        </h3>
                                                        <div
                                                            class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                                            @if ($item->product->brand)
                                                                <span
                                                                    class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded-md">
                                                                    {{ $item->product->brand->name }}
                                                                </span>
                                                            @endif
                                                            @if ($item->product->category)
                                                                <span
                                                                    class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-200 rounded-md">
                                                                    {{ $item->product->category->name }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Price -->
                                                        <div class="flex items-center space-x-2 mb-4">
                                                            <span
                                                                class="text-xl font-bold text-orange-500 dark:text-orange-400">
                                                                Rp{{ number_format($item->price, 0, ',', '.') }}
                                                            </span>
                                                            @if ($item->product->on_sale && $item->product->sale_price)
                                                                <span class="text-sm text-gray-400 line-through">
                                                                    Rp{{ number_format($item->product->price, 0, ',', '.') }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Quantity Controls -->
                                                        <div class="flex items-center space-x-3">
                                                            <span
                                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">Qty:</span>
                                                            <div
                                                                class="flex items-center border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                                                <button
                                                                    wire:click="updateQuantity({{ $item->id }}, {{ max(1, $item->qty - 1) }})"
                                                                    wire:loading.attr="disabled"
                                                                    @if ($item->qty <= 1) disabled @endif
                                                                    class="px-3 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M20 12H4"></path>
                                                                    </svg>
                                                                </button>

                                                                <span
                                                                    class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-medium min-w-[3rem] text-center">
                                                                    {{ $item->qty }}
                                                                </span>

                                                                <button
                                                                    wire:click="updateQuantity({{ $item->id }}, {{ $item->qty + 1 }})"
                                                                    wire:loading.attr="disabled"
                                                                    @if ($item->qty >= $item->product->stock_quantity) disabled @endif
                                                                    class="px-3 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                                ({{ $item->product->stock_quantity }} available)
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Remove Button & Subtotal -->
                                                    <div class="flex flex-col items-end space-y-3 ml-4">
                                                        <button wire:click="removeItem({{ $item->id }})"
                                                            wire:confirm="Remove this item from cart?"
                                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>

                                                        <div class="text-right">
                                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                                Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Cart Summary -->
                            <div class="lg:col-span-1">
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sticky top-6">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Order Summary</h2>

                                    <div class="space-y-4">
                                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                            <span>Subtotal ({{ $this->cart->total_items }} items)</span>
                                            <span>Rp{{ number_format($this->cart->subtotal, 0, ',', '.') }}</span>
                                        </div>

                                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                            <span>Shipping</span>
                                            <span class="text-green-600 dark:text-green-400">Calculated at
                                                checkout</span>
                                        </div>

                                        @if ($this->cart->discount_total > 0)
                                            <div class="flex justify-between text-green-600 dark:text-green-400">
                                                <span>Discount</span>
                                                <span>-Rp{{ number_format($this->cart->discount_total, 0, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                            <div
                                                class="flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                                                <span>Total</span>
                                                <span class="text-orange-500 dark:text-orange-400">
                                                    Rp{{ number_format($this->cart->subtotal - $this->cart->discount_total, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-8 space-y-3">
                                        <a href="/checkout"
                                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 text-white py-4 px-6 rounded-xl font-semibold hover:from-orange-600 hover:to-orange-700 dark:hover:from-orange-700 dark:hover:to-orange-800 transform hover:scale-105 transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                </path>
                                            </svg>
                                            Proceed to Checkout
                                        </a>

                                        <a href="/products"
                                            class="w-full bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                            </svg>
                                            Continue Shopping
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Empty Cart State -->
                        <div class="text-center py-20">
                            <div class="max-w-md mx-auto">
                                <!-- Empty Cart Icon -->
                                <div
                                    class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-3xl flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2">
                                        </path>
                                    </svg>
                                </div>

                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">Your Cart is Empty
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-8">
                                    Looks like you haven't added any items to your cart yet. Start shopping to fill it
                                    up!
                                </p>

                                <a href="/products"
                                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Start Shopping
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Flash Messages -->
                    @if (session()->has('success'))
                        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50"
                            x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform translate-y-2" x-init="setTimeout(() => show = false, 5000)">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50"
                            x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform translate-y-2" x-init="setTimeout(() => show = false, 5000)">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-layouts.public-app>
    </div>
@endvolt
