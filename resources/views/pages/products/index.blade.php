<?php

use App\Models\Product;
use App\Models\Brand;
use function Livewire\Volt\{state, computed, mount};

state([
    'search' => '',
    'brand_filter' => '',
    'price_range' => 'all',
]);

$products = computed(function () {
    $query = Product::with(['brand', 'category'])->where('is_active', true);

    if ($this->search) {
        $query->where('name', 'like', '%' . $this->search . '%');
    }

    if ($this->brand_filter) {
        $query->where('brand_id', $this->brand_filter);
    }

    if ($this->price_range !== 'all') {
        switch ($this->price_range) {
            case 'under_500k':
                $query->where('price', '<', 500000);
                break;
            case '500k_1m':
                $query->whereBetween('price', [500000, 1000000]);
                break;
            case 'over_1m':
                $query->where('price', '>', 1000000);
                break;
        }
    }

    return $query->paginate(12);
});

$brands = computed(function () {
    return Brand::where('is_active', true)->orderBy('name')->get();
});

?>
@volt
    <div>
        <x-layouts.public-app>
            <!-- Hero Section -->
            <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
                <div class="absolute inset-0"
                    style="background-image: radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(147, 51, 234, 0.1) 0%, transparent 50%);">
                </div>

                <div class="relative container mx-auto px-4 py-20">
                    <div class="text-center max-w-4xl mx-auto">
                        <!-- Badge -->
                        <div
                            class="inline-flex items-center px-4 py-2 bg-blue-600/20 backdrop-blur-sm border border-blue-400/30 rounded-full text-blue-300 text-sm font-medium mb-6">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            Koleksi Terbaru 2025
                        </div>

                        <h1 class="text-5xl md:text-6xl font-bold mb-6">
                            Koleksi
                            <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                                Sepatu
                            </span>
                        </h1>
                        <p class="text-xl text-slate-300 mb-8 leading-relaxed">
                            Temukan sepatu impian Anda dari koleksi premium dengan desain terdepan,<br>
                            kualitas terbaik, dan kenyamanan maksimal untuk setiap langkah Anda.
                        </p>
                    </div>
                </div>

                <!-- Decorative Elements -->
                <div class="absolute top-20 left-10 w-20 h-20 bg-blue-500/10 rounded-full blur-xl"></div>
                <div class="absolute bottom-20 right-10 w-32 h-32 bg-purple-500/10 rounded-full blur-xl"></div>
            </div>

            <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-slate-900 dark:to-slate-800">
                <div class="container mx-auto px-4 py-12">
                    <!-- Advanced Filters -->
                    <div class="relative mb-12">
                        <div
                            class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-8">
                            <!-- Filter Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-semibold text-slate-800 dark:text-white">Filter Produk</h2>
                                </div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ $this->products->total() }} produk ditemukan
                                </div>
                            </div>

                            <div class="grid md:grid-cols-3 gap-6">
                                <!-- Search Input -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            Cari Produk
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" wire:model.live="search" placeholder="Masukkan nama sepatu..."
                                            class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-slate-800 dark:text-white placeholder-slate-400">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Brand Filter -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                </path>
                                            </svg>
                                            Brand
                                        </span>
                                    </label>
                                    <select wire:model.live="brand_filter"
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-slate-800 dark:text-white">
                                        <option value="">Semua Brand</option>
                                        @foreach ($this->brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Price Range -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            Rentang Harga
                                        </span>
                                    </label>
                                    <select wire:model.live="price_range"
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-slate-800 dark:text-white">
                                        <option value="all">Semua Harga</option>
                                        <option value="under_500k">Dibawah Rp 500rb</option>
                                        <option value="500k_1m">Rp 500rb - 1jt</option>
                                        <option value="over_1m">Diatas Rp 1jt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                        @foreach ($this->products as $product)
                            <div class="group relative h-full">
                                <a href="/products/{{ $product->slug }}" class="block h-full">
                                    <div
                                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-slate-200/50 dark:border-slate-700/50 group-hover:border-blue-300/50 dark:group-hover:border-blue-600/50 group-hover:-translate-y-2 h-full flex flex-col">
                                        <!-- Product Image -->
                                        <div
                                            class="relative h-64 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 overflow-hidden">
                                            @if ($product->first_image)
                                                <img src="{{ Storage::url($product->first_image) }}" alt="{{ $product->name }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <div
                                                        class="w-16 h-16 bg-gradient-to-br from-slate-300 to-slate-400 dark:from-slate-600 dark:to-slate-500 rounded-2xl flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-slate-500 dark:text-slate-400"
                                                            fill="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Overlay Gradient -->
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-100">
                                            </div>

                                            <!-- Stock Badge -->
                                            <div class="absolute top-4 right-4">
                                                @if ($product->in_stock && $product->stock_quantity > 0)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-green-500/90 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                                        <div class="w-2 h-2 bg-green-300 rounded-full mr-2 animate-pulse">
                                                        </div>
                                                        {{ $product->stock_quantity }} stok
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-red-500/90 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                                        <div class="w-2 h-2 bg-red-300 rounded-full mr-2"></div>
                                                        Habis
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Sale Badge -->
                                            @if ($product->on_sale)
                                                <div class="absolute top-4 left-4">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-red-500/90 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                                        SALE
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="p-6 space-y-4 flex-1 flex flex-col">
                                            <!-- Brand -->
                                            <div class="flex items-center justify-between">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium rounded-full">
                                                    {{ $product->brand->name ?? 'No Brand' }}
                                                </span>
                                                <div class="flex items-center space-x-1">
                                                    @for ($i = 0; $i < 5; $i++)
                                                        <svg class="w-3 h-3 text-yellow-400" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                            </path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>

                                            <!-- Product Name -->
                                            <div class="flex-1">
                                                <h3
                                                    class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 min-h-[3.5rem] flex items-start">
                                                    {{ $product->name }}
                                                </h3>
                                            </div>

                                            <!-- Price -->
                                            <div class="flex items-center justify-between mt-auto">
                                                <div class="space-y-1">
                                                    @if ($product->on_sale && $product->sale_price)
                                                        <div class="flex items-center space-x-2">
                                                            <p
                                                                class="text-2xl font-bold bg-gradient-to-r from-red-600 to-red-500 bg-clip-text text-transparent">
                                                                Rp {{ number_format($product->sale_price, 0, ',', '.') }}
                                                            </p>
                                                            <p class="text-sm text-slate-500 line-through">
                                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                                            </p>
                                                        </div>
                                                    @else
                                                        <p
                                                            class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <!-- Action Button -->
                                                <div
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-100">
                                                    <div
                                                        class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white shadow-lg">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center">
                        <div
                            class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-lg rounded-2xl shadow-lg border border-white/20 dark:border-slate-700/50 p-4">
                            {{ $this->products->links() }}
                        </div>
                    </div>

                    <!-- Empty State -->
                    @if ($this->products->count() == 0)
                        <div class="text-center py-20">
                            <div class="max-w-md mx-auto">
                                <!-- Empty State Icon -->
                                <div
                                    class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-3xl flex items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2" />
                                    </svg>
                                </div>

                                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-3">Tidak Ada Produk</h3>
                                <p class="text-slate-600 dark:text-slate-400 mb-8">
                                    Tidak ada produk yang sesuai dengan filter yang dipilih. Coba ubah kriteria pencarian
                                    Anda.
                                </p>

                                <!-- Reset Filters Button -->
                                <button
                                    wire:click="$set('search', ''); $set('brand_filter', ''); $set('price_range', 'all')"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all duration-400 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Reset Filter
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-layouts.public-app>
    </div>
@endvolt
