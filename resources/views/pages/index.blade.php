<?php

use App\Models\Product;
use function Livewire\Volt\{state, computed, mount};

state(['featured_products' => []]);

$loadFeaturedProducts = computed(function () {
    // Ambil produk aktif dari database dengan limit 4, sesuai dengan migration
    return Product::with(['brand', 'category']) // Load relationships
        ->where('is_active', true)
        ->where('in_stock', true) // sesuai dengan field di migration
        ->orderBy('created_at', 'desc')
        ->limit(4)
        ->get();
});

?>

@volt
    <div>
        <x-layouts.public-app>
            <div
                class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
                <!-- Hero Section -->
                <section class="relative overflow-hidden pt-20 pb-16">
                    <!-- Background Pattern -->
                    <div
                        class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))] dark:bg-grid-slate-700/25 dark:[mask-image:linear-gradient(0deg,rgba(255,255,255,0.1),rgba(255,255,255,0.5))]">
                    </div>

                    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid lg:grid-cols-12 gap-8 items-center">
                            <!-- Content -->
                            <div class="lg:col-span-6 space-y-8">
                                <!-- Badge -->
                                <div
                                    class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-medium border border-blue-200 dark:border-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Koleksi Terbaru 2025
                                </div>

                                <!-- Heading -->
                                <div class="space-y-4">
                                    <h1 class="text-5xl lg:text-7xl font-black leading-tight">
                                        <span
                                            class="bg-gradient-to-r from-gray-900 via-blue-800 to-blue-600 bg-clip-text text-transparent dark:from-white dark:via-blue-300 dark:to-blue-400">
                                            Temukan
                                        </span>
                                        <br>
                                        <span class="text-gray-900 dark:text-white">Sepatu</span>
                                        <br>
                                        <span
                                            class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                            Impian Anda
                                        </span>
                                    </h1>
                                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-lg leading-relaxed">
                                        Koleksi sepatu premium dengan desain terdepan, kualitas terbaik, dan kenyamanan
                                        maksimal untuk setiap langkah Anda.
                                    </p>
                                </div>

                                <!-- CTA Buttons -->
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="/products"
                                        class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transform hover:scale-105 transition-all duration-100 shadow-xl hover:shadow-2xl">
                                        <span class="flex items-center">
                                            Jelajahi Koleksi
                                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </span>
                                    </a>
                                    <a href="#featured"
                                        class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 transform hover:scale-105 transition-all duration-400 shadow-lg hover:shadow-xl">
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                                </path>
                                            </svg>
                                            Produk Unggulan
                                        </span>
                                    </a>
                                </div>

                                <!-- Trust Indicators -->
                                <div class="flex items-center space-x-8 pt-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex -space-x-2">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full border-2 border-white dark:border-gray-800">
                                            </div>
                                            <div
                                                class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full border-2 border-white dark:border-gray-800">
                                            </div>
                                            <div
                                                class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full border-2 border-white dark:border-gray-800">
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">10K+ Pelanggan
                                            Puas</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="flex text-yellow-400">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">4.9/5
                                            Rating</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Hero Image -->
                            <div class="lg:col-span-6 relative">
                                <div class="relative">
                                    <!-- Decorative elements -->
                                    <div
                                        class="absolute -top-4 -left-4 w-72 h-72 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse">
                                    </div>
                                    <div
                                        class="absolute -bottom-4 -right-4 w-72 h-72 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000">
                                    </div>

                                    <!-- Main Image -->
                                    <div
                                        class="relative bg-gradient-to-br from-white to-gray-50 dark:from-white dark:to-gray-300 rounded-3xl p-8 shadow-2xl border border-gray-200 dark:border-gray-700">
                                        <img src="/img/nike-logo.png" alt="Shoes Collection"
                                            class="w-full h-auto object-contain transform hover:scale-105 transition-transform duration-500 drop-shadow-2xl">

                                        <!-- Floating Cards -->
                                        <div
                                            class="absolute -left-4 top-1/4 bg-white dark:bg-gray-800 rounded-xl p-3 shadow-lg border border-gray-200 dark:border-gray-700 transform rotate-3 hover:rotate-0 transition-transform">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Premium
                                                    Quality</span>
                                            </div>
                                        </div>

                                        <div
                                            class="absolute -right-4 bottom-1/4 bg-white dark:bg-gray-800 rounded-xl p-3 shadow-lg border border-gray-200 dark:border-gray-700 transform -rotate-3 hover:rotate-0 transition-transform">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Free
                                                    Shipping</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Featured Products -->
                <section id="featured" class="py-20 bg-white dark:bg-gray-900">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center space-y-4 mb-16">
                            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white">
                                Produk <span
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Unggulan</span>
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                                Pilihan terbaik dari koleksi kami yang paling diminati pelanggan
                            </p>
                        </div>

                        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @forelse ($this->loadFeaturedProducts as $product)
                                <div
                                    class="group relative bg-white dark:bg-gray-800 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-400 overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-600 transform hover:-translate-y-2">
                                    <a href="/products/{{ $product->slug }}" class="block">
                                        <!-- Product Image -->
                                        <div
                                            class="relative h-64 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 overflow-hidden">
                                            @if ($product->image)
                                                @php
                                                    // Handle JSON image field
                                                    $images = is_string($product->image)
                                                        ? json_decode($product->image, true)
                                                        : $product->image;
                                                    $firstImage = is_array($images)
                                                        ? $images[0] ?? null
                                                        : $product->image;
                                                @endphp
                                                @if ($firstImage)
                                                    <img src="{{ Storage::url($firstImage) }}" alt="{{ $product->name }}"
                                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif

                                            <!-- Sale Badge -->
                                            @if ($product->on_sale)
                                                <div
                                                    class="absolute top-4 left-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                                    SALE
                                                </div>
                                            @endif

                                            <!-- Featured Badge -->
                                            @if ($product->is_featured)
                                                <div
                                                    class="absolute top-4 left-4 bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                                    FEATURED
                                                </div>
                                            @endif

                                            <!-- Wishlist Button -->
                                            <button
                                                class="absolute top-4 right-4 w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 dark:hover:bg-red-900/20">
                                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 hover:text-red-500"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="p-6 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span
                                                    class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-full">
                                                    {{ $product->brand ? $product->brand->name : 'No Brand' }}
                                                </span>
                                                @if ($product->in_stock)
                                                    <span
                                                        class="inline-flex items-center text-xs font-medium text-green-700 dark:text-green-400">
                                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                                        Tersedia
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center text-xs font-medium text-red-700 dark:text-red-400">
                                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                                        Habis
                                                    </span>
                                                @endif
                                            </div>

                                            <h3
                                                class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                                {{ $product->name }}
                                            </h3>

                                            <!-- Category -->
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $product->category ? $product->category->name : 'Uncategorized' }}
                                            </div>

                                            <div class="flex items-center justify-between">
                                                <div class="space-y-1">
                                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <div
                                                        class="w-10 h-10 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center text-white transform translate-x-2 group-hover:translate-x-0 transition-transform">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 text-lg">Belum ada produk unggulan tersedia
                                    </p>
                                </div>
                            @endforelse
                        </div>

                        <!-- View All Products Button -->
                        <div class="text-center mt-16">
                            <a href="/products"
                                class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transform hover:scale-105 transition-all duration-400 shadow-xl hover:shadow-2xl">
                                <span class="flex items-center">
                                    Lihat Semua Produk
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Features Section -->
                <section
                    class="py-20 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center space-y-4 mb-16">
                            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white">
                                Mengapa Memilih <span
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Kami?</span>
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                                Komitmen kami untuk memberikan pengalaman berbelanja sepatu terbaik
                            </p>
                        </div>

                        <div class="grid md:grid-cols-3 gap-8">
                            <div
                                class="group relative text-center p-8 bg-white dark:bg-gray-800 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-400 border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-600 transform hover:-translate-y-2">
                                <div class="relative">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-lg group-hover:shadow-xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-100">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3
                                        class="text-2xl font-bold mb-4 text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        Kualitas Premium</h3>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Setiap produk telah
                                        melewati kontrol kualitas ketat dengan standar internasional untuk memastikan
                                        kepuasan Anda.</p>
                                </div>
                            </div>

                            <div
                                class="group relative text-center p-8 bg-white dark:bg-gray-800 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-100 border border-gray-100 dark:border-gray-700 hover:border-purple-200 dark:hover:border-purple-600 transform hover:-translate-y-2">
                                <div class="relative">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow transform group-hover:scale-110 group-hover:rotate-3 duration-100">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3
                                        class="text-2xl font-bold mb-4 text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                        Harga Terjangkau</h3>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Dapatkan sepatu berkualitas
                                        premium dengan harga yang bersahabat. Investasi terbaik untuk kenyamanan kaki Anda.
                                    </p>
                                </div>
                            </div>

                            <div
                                class="group relative text-center p-8 bg-white dark:bg-gray-800 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-100 border border-gray-100 dark:border-gray-700 hover:border-green-200 dark:hover:border-green-600 transform hover:-translate-y-2">
                                <div class="relative">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-lg group-hover:shadow-xl transform group-hover:scale-110 group-hover:rotate-3 transition-transform duration-100">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <h3
                                        class="text-2xl font-bold mb-4 text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                        Pengiriman Express</h3>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Pengiriman cepat ke seluruh
                                        Indonesia dengan gratis ongkir untuk pembelian di atas Rp 500.000. Pesanan sampai
                                        dalam 1-3 hari.</p>
                                </div>
                            </div>

                        </div>

                        <!-- Additional Stats -->
                        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">10K+</div>
                                <div class="text-gray-600 dark:text-gray-400">Pelanggan Senang</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">500+</div>
                                <div class="text-gray-600 dark:text-gray-400">Produk Tersedia</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600 dark:text-green-400">50+</div>
                                <div class="text-gray-600 dark:text-gray-400">Brand Ternama</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">24/7</div>
                                <div class="text-gray-600 dark:text-gray-400">Customer Support</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </x-layouts.public-app>
    </div>
@endvolt
