<?php

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\{state, computed, mount, rules};

// Untuk Laravel Folio dengan parameter [slug]
$slug = request()->route()->parameter('slug');

// State untuk menyimpan product
state([
    'product' => null,
    'quantity' => 1,
    'isLoading' => false,
]);

// Mount untuk load product
mount(function () use ($slug) {
    $this->product = Product::with(['brand', 'category'])
        ->where('slug', $slug)
        ->first();

    if (!$this->product) {
        abort(404, 'Product not found');
    }
});

$maxQuantity = computed(fn() => $this->product ? $this->product->stock_quantity : 0);
$subtotal = computed(fn() => $this->quantity * ($this->product && $this->product->on_sale && $this->product->sale_price ? $this->product->sale_price : ($this->product ? $this->product->price : 0)));

rules([
    'quantity' => 'required|integer|min:1',
]);

$increaseQuantity = function () {
    if ($this->quantity < $this->maxQuantity) {
        $this->quantity++;
    }
};

$decreaseQuantity = function () {
    if ($this->quantity > 1) {
        $this->quantity--;
    }
};

$addToCart = function (CartService $cartService) {
    $this->validate();

    if (!$this->product->in_stock || $this->product->stock_quantity < 1) {
        session()->flash('error', 'Produk sedang tidak tersedia');
        return;
    }

    if (!Auth::check()) {
        session()->flash('error', 'Silakan login terlebih dahulu');
        return;
    }

    $this->isLoading = true;

    $result = $cartService->addItem($this->product->id, $this->quantity, null, Auth::id());

    if ($result['success']) {
        session()->flash('success', $result['message']);
        $this->dispatch('cart-updated', count: $result['cart_count']);
    } else {
        session()->flash('error', $result['message']);
    }

    $this->isLoading = false;
};

$buyNow = function (CartService $cartService) {
    if (!Auth::check()) {
        session()->flash('error', 'Silakan login terlebih dahulu');
        return;
    }

    $result = $cartService->addItem($this->product->id, $this->quantity, null, Auth::id());

    if ($result['success']) {
        return redirect()->route('cart');
    } else {
        session()->flash('error', $result['message']);
    }
};

?>

<x-layouts.public-app>
    @volt
        <div>
            @if($product)
            <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
                <!-- Breadcrumb -->
                <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 dark:border-gray-700 pt-3">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-3 pb-5">
                        <nav class="flex items-center space-x-2 text-sm">
                            <a href="/"
                                class="text-gray-500 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition-colors">Home</a>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <a href="/products"
                                class="text-gray-500 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition-colors">Products</a>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium truncate">{{ $product->name }}</span>
                        </nav>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                        <!-- Product Images Section -->
                        <div class="lg:col-span-5">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden sticky top-6">
                                <div
                                    class="aspect-square relative bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                                    @if ($product->first_image)
                                        <img src="{{ Storage::url($product->first_image) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <div class="text-center text-gray-400 dark:text-gray-500">
                                                <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="text-lg font-medium">No Image Available</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Stock Badge -->
                                    <div class="absolute top-4 right-4">
                                        @if ($product->in_stock && $product->stock_quantity > 0)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-700">
                                                <div
                                                    class="w-2 h-2 bg-green-400 dark:bg-green-300 rounded-full mr-2 animate-pulse">
                                                </div>
                                                In Stock ({{ $product->stock_quantity }})
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-700">
                                                <div class="w-2 h-2 bg-red-400 dark:bg-red-300 rounded-full mr-2"></div>
                                                Sold Out
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Sale Badge -->
                                    @if ($product->on_sale)
                                        <div class="absolute top-4 left-4">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-700">
                                                DISKON
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Product Info Section -->
                        <div class="lg:col-span-7">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                                <!-- Brand & Category -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @if ($product->brand)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 border border-blue-200 dark:border-blue-700">
                                            {{ $product->brand->name }}
                                        </span>
                                    @endif
                                    @if ($product->category)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-50 dark:bg-purple-900 text-purple-700 dark:text-purple-200 border border-purple-200 dark:border-purple-700">
                                            {{ $product->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Product Title -->
                                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
                                    {{ $product->name }}
                                </h1>

                                <!-- Price -->
                                <div class="mb-8">
                                    <div class="flex items-baseline space-x-3">
                                        @if ($product->on_sale && $product->sale_price)
                                            <span
                                                class="text-3xl lg:text-4xl font-bold text-orange-500 dark:text-orange-400">
                                                Rp{{ number_format($product->sale_price, 0, ',', '.') }}
                                            </span>
                                            <span class="text-lg text-gray-400 dark:text-gray-500 line-through">
                                                Rp{{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                            @php
                                                $discount = round(
                                                    (($product->price - $product->sale_price) / $product->price) * 100,
                                                );
                                            @endphp
                                            <span
                                                class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-200 text-sm font-semibold rounded">
                                                -{{ $discount }}%
                                            </span>
                                        @else
                                            <span
                                                class="text-3xl lg:text-4xl font-bold text-orange-500 dark:text-orange-400">
                                                Rp{{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if ($product->in_stock && $product->stock_quantity > 0)
                                    <!-- Quantity Selector -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            Quantity
                                        </label>
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="flex items-center border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                                                <button wire:click="decreaseQuantity"
                                                    class="px-4 py-4 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                    @if($quantity <= 1) disabled @endif
                                                    aria-label="Decrease quantity">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input type="number" wire:model.live="quantity" min="1"
                                                    max="{{ $this->maxQuantity }}"
                                                    class="w-20 py-3 text-center border-0 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold focus:ring-0 focus:outline-none"
                                                    aria-label="Quantity">
                                                <button wire:click="increaseQuantity"
                                                    class="px-4 py-4 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                    @if($quantity >= $this->maxQuantity) disabled @endif
                                                    aria-label="Increase quantity">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Stok tersedia: {{ $product->stock_quantity }} pcs
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Real-time Subtotal -->
                                    <div
                                        class="mb-6 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-xl">
                                        <div class="flex items-center justify-between">
                                            <span class="text-orange-700 dark:text-orange-300 font-medium">Subtotal:</span>
                                            <span class="text-2xl font-bold text-orange-600 dark:text-orange-400"
                                                aria-live="polite">
                                                Rp{{ number_format($this->subtotal, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row gap-3 mb-6">
                                    @if ($product->in_stock && $product->stock_quantity > 0)
                                        <button wire:click="addToCart" wire:loading.attr="disabled"
                                            class="flex-1 bg-white dark:bg-gray-800 border-2 border-orange-500 dark:border-orange-400 text-orange-500 dark:text-orange-400 py-3 px-6 rounded-xl font-semibold hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors flex items-center justify-center group disabled:opacity-50">
                                            <div wire:loading.remove wire:target="addToCart" class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2" />
                                                </svg>
                                                Add to Cart
                                            </div>
                                            <div wire:loading wire:target="addToCart" class="flex items-center">
                                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-orange-500"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </button>
                                        <button wire:click="buyNow"
                                            class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-orange-600 hover:to-orange-700 dark:hover:from-orange-700 dark:hover:to-orange-800 transform hover:scale-105 transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Buy Now
                                        </button>
                                    @else
                                        <button
                                            class="w-full bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 py-3 px-6 rounded-xl font-semibold cursor-not-allowed flex items-center justify-center"
                                            disabled>
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636" />
                                            </svg>
                                            Out of Stock
                                        </button>
                                    @endif
                                </div>

                                @if (session()->has('success'))
                                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl"
                                        role="alert" aria-live="polite">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-500 dark:text-green-400 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span
                                                class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if (session()->has('error'))
                                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl"
                                        role="alert" aria-live="polite">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-red-500 dark:text-red-400 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span
                                                class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Description -->
                            @if ($product->description)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-orange-500 dark:text-orange-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Product Description
                                    </h3>
                                    <div class="prose prose-gray dark:prose-invert max-w-none">
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                            {!! nl2br(e($product->description)) !!}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @else
                <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Product Not Found</h1>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">The product you're looking for doesn't exist.</p>
                        <a href="/products" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                            Back to Products
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endvolt
</x-layouts.public-app>
