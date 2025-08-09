<?php

use App\Services\CartService;
use App\Services\CheckoutService;
use function Livewire\Volt\{state, computed, mount, rules};

state([
    'shipping_name' => '',
    'shipping_phone' => '',
    'shipping_street' => '',
    'shipping_city' => '',
    'shipping_province' => '',
    'shipping_postal_code' => '',
    'shipping_method' => 'regular',
    'notes' => '',
    'isProcessing' => false,
]);

rules([
    'shipping_name' => 'required|min:2|max:100',
    'shipping_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
    'shipping_street' => 'required|min:10|max:255',
    'shipping_city' => 'required|min:2|max:100',
    'shipping_province' => 'required|min:2|max:100',
    'shipping_postal_code' => 'required|digits:5',
    'shipping_method' => 'required|in:regular,express,same_day',
]);

$cart = computed(function (CartService $cartService) {
    $cart = $cartService->getOrCreateCart()->load(['items.product.brand']);

    if ($cart->items->isEmpty()) {
        return redirect('/cart');
    }

    return $cart;
});

$shippingMethods = computed(function (CheckoutService $checkoutService) {
    return $checkoutService->getShippingMethods();
});

$shippingCost = computed(function (CheckoutService $checkoutService) {
    return $checkoutService->calculateShipping($this->shipping_method, $this->shipping_city);
});

$grandTotal = computed(function () {
    return $this->cart->subtotal + $this->shippingCost;
});

mount(function () {
    // Pre-fill with user data if authenticated
    if (auth()->check()) {
        $user = auth()->user();
        $this->shipping_name = $user->name ?? '';
        $this->shipping_phone = $user->phone ?? '';
    }
});

$placeOrder = function (CheckoutService $checkoutService) {
    $this->validate();

    $this->isProcessing = true;

    $shippingData = [
        'shipping_name' => $this->shipping_name,
        'shipping_phone' => $this->shipping_phone,
        'shipping_street' => $this->shipping_street,
        'shipping_city' => $this->shipping_city,
        'shipping_province' => $this->shipping_province,
        'shipping_postal_code' => $this->shipping_postal_code,
        'shipping_method' => $this->shipping_method,
        'notes' => $this->notes,
    ];

    $result = $checkoutService->processCheckout($this->cart, $shippingData, 'bank_transfer');

    if ($result['success']) {
        session()->flash('success', $result['message']);
        return redirect('/orders/' . $result['order']->id . '/payment');
    } else {
        session()->flash('error', $result['message']);

        if (isset($result['issues'])) {
            // Handle stock issues by redirecting back to cart
            return redirect('/cart')->with('stock_issues', $result['issues']);
        }
    }

    $this->isProcessing = false;
};

?>

@volt
<div>
    <x-layouts.public-app>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Checkout</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Complete your order</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Shipping Information -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Shipping Information
                            </h2>

                            <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="shipping_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Full Name *
                                    </label>
                                    <input
                                        type="text"
                                        id="shipping_name"
                                        wire:model="shipping_name"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('shipping_name')  @enderror"
                                        placeholder="Enter your full name">
                                    @error('shipping_name')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="shipping_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Phone Number *
                                    </label>
                                    <input
                                        type="tel"
                                        id="shipping_phone"
                                        wire:model="shipping_phone"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('shipping_phone')  @enderror"
                                        placeholder="Enter your phone number">
                                    @error('shipping_phone')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="shipping_street" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Street Address *
                                    </label>
                                    <textarea
                                        id="shipping_street"
                                        wire:model="shipping_street"
                                        rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('shipping_street')  @enderror"
                                        placeholder="Enter your complete street address"></textarea>
                                    @error('shipping_street')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="shipping_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        City *
                                    </label>
                                    <input
                                        type="text"
                                        id="shipping_city"
                                        wire:model.live="shipping_city"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('shipping_city')  @enderror"
                                        placeholder="Enter your city">
                                    @error('shipping_city')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="shipping_province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Province *
                                    </label>
                                    <input
                                        type="text"
                                        id="shipping_province"
                                        wire:model="shipping_province"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('shipping_province') 0 @enderror"
                                        placeholder="Enter your province">
                                    @error('shipping_province')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Postal Code *
                                    </label>
                                    <input
                                        type="text"
                                        id="shipping_postal_code"
                                        wire:model="shipping_postal_code"
                                        maxlength="5"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('shipping_postal_code')  @enderror"
                                        placeholder="12345">
                                    @error('shipping_postal_code')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </form>
                        </div>

                        <!-- Shipping Method -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Shipping Method
                            </h2>

                            <div class="space-y-3">
                                @foreach($this->shippingMethods as $key => $method)
                                    <label class="flex items-start space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors @if($shipping_method === $key) bg-orange-50 dark:bg-orange-900/20 @endif">
                                        <input
                                            type="radio"
                                            wire:model.live="shipping_method"
                                            value="{{ $key }}"
                                            class="mt-1 text-orange-500 focus:ring-orange-500">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $method['name'] }}</h3>
                                                <span class="font-bold text-orange-500 dark:text-orange-400">
                                                    Rp{{ number_format($method['price'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $method['description'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                Order Notes (Optional)
                            </h2>

                            <textarea
                                wire:model="notes"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="Any special instructions for your order..."></textarea>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sticky top-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Order Summary</h2>

                            <!-- Order Items -->
                            <div class="space-y-4 mb-6">
                                @foreach($this->cart->items as $item)
                                    <div class="flex items-center space-x-3">
                                        <div class="relative">
                                            <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg overflow-hidden">
                                                @if($item->product->first_image)
                                                    <img src="{{ Storage::url($item->product->first_image) }}"
                                                         alt="{{ $item->product->name }}"
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                                {{ $item->qty }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $item->product->name }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Rp{{ number_format($item->price, 0, ',', '.') }} × {{ $item->qty }}
                                            </p>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
                                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                    <span>Subtotal ({{ $this->cart->total_items }} items)</span>
                                    <span>Rp{{ number_format($this->cart->subtotal, 0, ',', '.') }}</span>
                                </div>

                                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                    <span>Shipping</span>
                                    <span>Rp{{ number_format($this->shippingCost, 0, ',', '.') }}</span>
                                </div>

                                @if($this->cart->discount_total > 0)
                                    <div class="flex justify-between text-green-600 dark:text-green-400">
                                        <span>Discount</span>
                                        <span>-Rp{{ number_format($this->cart->discount_total, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                                        <span>Total</span>
                                        <span class="text-orange-500 dark:text-orange-400">
                                            Rp{{ number_format($this->grandTotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8">
                                <button
                                    wire:click="placeOrder"
                                    wire:loading.attr="disabled"
                                    class="w-full bg-gradient-to-r from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 text-white py-4 px-6 rounded-xl font-semibold hover:from-orange-600 hover:to-orange-700 dark:hover:from-orange-700 dark:hover:to-orange-800 transform hover:scale-105 transition-all shadow-lg hover:shadow-xl flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                    <div wire:loading.remove wire:target="placeOrder" class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Place Order
                                    </div>
                                    <div wire:loading wire:target="placeOrder" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </div>
                                </button>

                                <div class="mt-4 text-center">
                                    <a href="/cart" class="text-sm text-gray-500 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition-colors">
                                        ← Back to Cart
                                    </a>
                                </div>
                            </div>

                            <!-- Payment Method Info -->
                            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <h3 class="font-medium text-gray-900 dark:text-white mb-2">Payment Method</h3>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Bank Transfer (You'll be redirected to payment instructions)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50"
                         x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform translate-y-2"
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50"
                         x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform translate-y-2"
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
