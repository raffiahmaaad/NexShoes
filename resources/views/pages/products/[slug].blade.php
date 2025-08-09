<?php

use App\Models\Product;

// Untuk Laravel Folio dengan parameter [slug]
$slug = request()->route()->parameter('slug');

$product = Product::with(['brand', 'category'])
    ->where('slug', $slug)
    ->first();

if (!$product) {
    abort(404, 'Product not found');
}

?>

<x-layouts.public-app>
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

                <!-- Enhanced Product Images Section with Advanced Zoom -->
                <div class="lg:col-span-5">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden sticky top-6"
                        x-data="{
                            currentImageIndex: 0,
                            images: {{ $product->has_images ? json_encode($product->images) : json_encode([$product->first_image ?? '']) }},
                            showFullscreen: false,
                            isZoomed: false,
                            zoomLevel: 1,
                            maxZoom: 3,
                            zoomStep: 0.5,
                            imagePosition: { x: 0, y: 0 },
                            isDragging: false,
                            dragStart: { x: 0, y: 0 },
                        
                            nextImage() {
                                this.currentImageIndex = (this.currentImageIndex + 1) % this.images.length;
                                this.resetZoom();
                            },
                            prevImage() {
                                this.currentImageIndex = this.currentImageIndex === 0 ? this.images.length - 1 : this.currentImageIndex - 1;
                                this.resetZoom();
                            },
                            goToImage(index) {
                                this.currentImageIndex = index;
                                this.resetZoom();
                            },
                            openFullscreen() {
                                this.showFullscreen = true;
                                this.resetZoom();
                                document.body.classList.add('modal-open');
                            },
                            closeFullscreen() {
                                this.showFullscreen = false;
                                this.resetZoom();
                                document.body.classList.remove('modal-open');
                            },
                        
                            // Zoom Functions
                            zoomIn() {
                                if (this.zoomLevel < this.maxZoom) {
                                    this.zoomLevel = Math.min(this.maxZoom, this.zoomLevel + this.zoomStep);
                                    this.isZoomed = this.zoomLevel > 1;
                                }
                            },
                            zoomOut() {
                                if (this.zoomLevel > 1) {
                                    this.zoomLevel = Math.max(1, this.zoomLevel - this.zoomStep);
                                    this.isZoomed = this.zoomLevel > 1;
                                    if (!this.isZoomed) {
                                        this.imagePosition = { x: 0, y: 0 };
                                    }
                                }
                            },
                            resetZoom() {
                                this.zoomLevel = 1;
                                this.isZoomed = false;
                                this.imagePosition = { x: 0, y: 0 };
                            },
                        
                            // Mouse/Touch Events for Zoom and Pan
                            handleWheel(event) {
                                event.preventDefault();
                                if (event.deltaY < 0) {
                                    this.zoomIn();
                                } else {
                                    this.zoomOut();
                                }
                            },
                        
                            startDrag(event) {
                                if (this.isZoomed) {
                                    this.isDragging = true;
                                    const clientX = event.touches ? event.touches[0].clientX : event.clientX;
                                    const clientY = event.touches ? event.touches[0].clientY : event.clientY;
                                    this.dragStart = {
                                        x: clientX - this.imagePosition.x,
                                        y: clientY - this.imagePosition.y
                                    };
                                    document.body.style.userSelect = 'none';
                                }
                            },
                        
                            drag(event) {
                                if (this.isDragging && this.isZoomed) {
                                    event.preventDefault();
                                    const clientX = event.touches ? event.touches[0].clientX : event.clientX;
                                    const clientY = event.touches ? event.touches[0].clientY : event.clientY;
                        
                                    this.imagePosition = {
                                        x: clientX - this.dragStart.x,
                                        y: clientY - this.dragStart.y
                                    };
                                }
                            },
                        
                            stopDrag() {
                                this.isDragging = false;
                                document.body.style.userSelect = '';
                            },
                        
                            // Double click to zoom
                            handleDoubleClick(event) {
                                if (this.isZoomed) {
                                    this.resetZoom();
                                } else {
                                    this.zoomLevel = 2;
                                    this.isZoomed = true;
                        
                                    // Calculate zoom position based on click location
                                    const rect = event.currentTarget.getBoundingClientRect();
                                    const x = event.clientX - rect.left;
                                    const y = event.clientY - rect.top;
                                    const centerX = rect.width / 2;
                                    const centerY = rect.height / 2;
                        
                                    this.imagePosition = {
                                        x: (centerX - x) * (this.zoomLevel - 1),
                                        y: (centerY - y) * (this.zoomLevel - 1)
                                    };
                                }
                            }
                        }" @keydown.escape.window="closeFullscreen()"
                        @fullscreen-close.window="closeFullscreen()">

                        <!-- Main Image Container with Enhanced Zoom -->
                        <div
                            class="aspect-square relative bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 group overflow-hidden">
                            @if ($product->first_image)
                                <!-- Main Image Display with Zoom -->
                                <template x-for="(image, index) in images" :key="index">
                                    <div x-show="currentImageIndex === index"
                                        class="w-full h-full absolute inset-0 cursor-zoom-in"
                                        :class="{
                                            'cursor-zoom-out': isZoomed,
                                            'cursor-grab': isZoomed && !
                                                isDragging,
                                            'cursor-grabbing': isDragging
                                        }"
                                        @wheel="handleWheel" @mousedown="startDrag" @mousemove="drag"
                                        @mouseup="stopDrag" @mouseleave="stopDrag" @touchstart="startDrag"
                                        @touchmove="drag" @touchend="stopDrag" @dblclick="handleDoubleClick">
                                        <img :src="`{{ Storage::url('') }}${image}`"
                                            :alt="`{{ $product->name }} - Image ${index + 1}`"
                                            class="w-full h-full object-cover transition-transform duration-300 ease-out select-none"
                                            :style="`transform: scale(${zoomLevel}) translate(${imagePosition.x / zoomLevel}px, ${imagePosition.y / zoomLevel}px)`"
                                            draggable="false">
                                    </div>
                                </template>

                                <!-- Zoom Controls -->
                                <div
                                    class="absolute bottom-4 left-4 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-all z-20">
                                    <button @click="zoomIn()" :disabled="zoomLevel >= maxZoom"
                                        class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm p-2 rounded-full shadow-lg hover:bg-white dark:hover:bg-gray-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-4 h-4 text-gray-700 dark:text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                    </button>
                                    <button @click="zoomOut()" :disabled="zoomLevel <= 1"
                                        class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm p-2 rounded-full shadow-lg hover:bg-white dark:hover:bg-gray-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-4 h-4 text-gray-700 dark:text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10h-3" />
                                        </svg>
                                    </button>
                                    <button @click="openFullscreen()"
                                        class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm p-2 rounded-full shadow-lg hover:bg-white dark:hover:bg-gray-700 transition-all">
                                        <svg class="w-4 h-4 text-gray-700 dark:text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Zoom Level Indicator -->
                                {{-- <div x-show="isZoomed"
                                    class="absolute top-4 left-4 bg-black/60 text-white px-3 py-1 rounded-full text-sm backdrop-blur-sm">
                                    <span x-text="Math.round(zoomLevel * 100)"></span>
                                </div> --}}

                                <!-- Navigation Arrows (only show if multiple images) -->
                                <template x-if="images.length > 1">
                                    <div>
                                        <!-- Previous Button -->
                                        <button @click="prevImage()"
                                            class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm p-2 rounded-full shadow-lg hover:bg-white dark:hover:bg-gray-700 transition-all opacity-0 group-hover:opacity-100 z-10">
                                            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>

                                        <!-- Next Button -->
                                        <button @click="nextImage()"
                                            class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm p-2 rounded-full shadow-lg hover:bg-white dark:hover:bg-gray-700 transition-all opacity-0 group-hover:opacity-100 z-10">
                                            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <!-- Image Counter (only show if multiple images) -->
                                <template x-if="images.length > 1">
                                    <div
                                        class="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm backdrop-blur-sm">
                                        <span x-text="currentImageIndex + 1"></span>/<span
                                            x-text="images.length"></span>
                                    </div>
                                </template>

                                <!-- Zoom Instructions -->
                                <div class="absolute bottom-16 left-4 opacity-0 group-hover:opacity-100 transition-all">
                                </div>
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
                                        In Stock
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
                                <div class="absolute top-4 right-20">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-700">
                                        DISKON
                                    </span>
                                </div>
                            @endif

                            <!-- Wishlist Button -->
                            <button
                                class="absolute top-14 right-4 p-2 rounded-full bg-white dark:bg-gray-700 shadow-md hover:shadow-lg transition-shadow group z-20">
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-300 group-hover:text-red-500 dark:group-hover:text-red-400 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Enhanced Thumbnail Gallery -->
                        @if ($product->has_images && count($product->images) > 1)
                            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex space-x-2 overflow-x-auto pb-2 pt-2 px-2">
                                    <template x-for="(image, index) in images" :key="index">
                                        <div @click="goToImage(index)"
                                            :class="currentImageIndex === index ?
                                                'border-orange-500 dark:border-orange-400 ring-2 ring-orange-200 dark:ring-orange-800' :
                                                'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'"
                                            class="flex-shrink-0 w-16 h-16 rounded-lg border-2 overflow-hidden cursor-pointer transition-all transform hover:scale-105">
                                            <img :src="`{{ Storage::url('') }}${image}`"
                                                :alt="`{{ $product->name }} thumbnail ${index + 1}`"
                                                class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        @endif

                        <!-- Enhanced Fullscreen Modal with Proper Centering -->
                        <div x-show="showFullscreen" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-black bg-opacity-95 z-[9999] flex items-center justify-center"
                            @click="closeFullscreen()" @keydown.escape.window="closeFullscreen()">

                            <!-- Close Button -->
                            <button @click="closeFullscreen()"
                                class="absolute top-4 right-4 text-white hover:text-gray-300 z-[10000] bg-black/30 hover:bg-black/50 rounded-full p-2 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- Fullscreen Zoom Controls -->
                            <div class="absolute top-4 left-4 flex space-x-2 z-[10000]">
                                <button @click.stop="zoomIn()" :disabled="zoomLevel >= maxZoom"
                                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm p-3 rounded-full text-white transition-all disabled:opacity-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                    </svg>
                                </button>
                                <button @click.stop="zoomOut()" :disabled="zoomLevel <= 1"
                                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm p-3 rounded-full text-white transition-all disabled:opacity-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10h-3" />
                                    </svg>
                                </button>
                                <button @click.stop="resetZoom()"
                                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm p-3 rounded-full text-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Main Image Container - FIXED CENTERING -->
                            <div class="absolute inset-0 flex items-center justify-center overflow-hidden" @click.stop>
                                <!-- Fullscreen Image -->
                                <template x-for="(image, index) in images" :key="index">
                                    <div x-show="currentImageIndex === index"
                                        class="flex items-center justify-center w-full h-full"
                                        :class="{ 'cursor-grab': isZoomed && !isDragging, 'cursor-grabbing': isDragging }"
                                        @wheel="handleWheel" @mousedown="startDrag" @mousemove="drag"
                                        @mouseup="stopDrag" @mouseleave="stopDrag" @touchstart="startDrag"
                                        @touchmove="drag" @touchend="stopDrag" @dblclick="handleDoubleClick">
                                        <img :src="`{{ Storage::url('') }}${image}`"
                                            :alt="`{{ $product->name }} - Image ${index + 1}`"
                                            class="max-w-[90vw] max-h-[90vh] object-contain transition-transform duration-300 ease-out select-none"
                                            :style="`transform: scale(${zoomLevel}) translate(${imagePosition.x / zoomLevel}px, ${imagePosition.y / zoomLevel}px)`"
                                            draggable="false">
                                    </div>
                                </template>

                                <!-- Fullscreen Navigation -->
                                <template x-if="images.length > 1">
                                    <div>
                                        <button @click.stop="prevImage()"
                                            class="absolute left-6 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/30 backdrop-blur-sm p-3 rounded-full text-white transition-all z-10">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>

                                        <button @click.stop="nextImage()"
                                            class="absolute right-6 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/30 backdrop-blur-sm p-3 rounded-full text-white transition-all z-10">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Fullscreen Zoom Level -->
                            {{-- <div x-show="isZoomed"
                                class="absolute top-20 left-4 bg-black/60 text-white px-4 py-2 rounded-full backdrop-blur-sm z-[10000]">
                                <span x-text="Math.round(zoomLevel * 100)"></span>
                            </div> --}}

                            <!-- Image Counter -->
                            <template x-if="images.length > 1">
                                <div
                                    class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-black/60 text-white px-4 py-2 rounded-full backdrop-blur-sm">
                                    <span x-text="currentImageIndex + 1"></span> / <span
                                        x-text="images.length"></span>
                                </div>
                            </template>
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
                            @if ($product->is_featured)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-700">
                                    Featured
                                </span>
                            @endif
                        </div>

                        <!-- Product Title -->
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
                            {{ $product->name }}
                        </h1>

                        <!-- Rating & Reviews (placeholder) -->
                        <div class="flex items-center mb-6 space-x-4">
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 dark:text-yellow-300">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">4.8 (1,234 reviews)</span>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">•</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">2,567 sold</div>
                        </div>

                        <!-- Price -->
                        <div class="mb-8">
                            <div class="flex items-baseline space-x-3">
                                @if ($product->on_sale && $product->sale_price)
                                    <span class="text-3xl lg:text-4xl font-bold text-orange-500 dark:text-orange-400">
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
                                    <span class="text-3xl lg:text-4xl font-bold text-orange-500 dark:text-orange-400">
                                        Rp{{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Promo Banner (only show if on sale) -->
                        @if ($product->on_sale)
                            <div
                                class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 border border-orange-200 dark:border-orange-700 rounded-xl p-4 mb-6">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-orange-500 dark:text-orange-400 mr-2" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-orange-700 dark:text-orange-300 font-medium">Special Sale
                                        Price!</span>
                                </div>
                            </div>
                        @endif

                        <!-- Stock Info -->
                        <div class="mb-6">
                            @if ($product->in_stock && $product->stock_quantity > 0)
                                <div
                                    class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 dark:text-green-400 mr-2"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-green-800 dark:text-green-200 font-medium">Ready Stock</span>
                                    </div>
                                    <span
                                        class="text-green-600 dark:text-green-300 text-sm">{{ $product->stock_quantity }}
                                        pieces available</span>
                                </div>
                            @else
                                <div
                                    class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl">
                                    <svg class="w-5 h-5 text-red-500 dark:text-red-400 mr-2" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-red-800 dark:text-red-200 font-medium">Out of Stock</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons (No Quantity Selector) -->
                        <div class="flex flex-col sm:flex-row gap-3 mb-6">
                            @if ($product->in_stock && $product->stock_quantity > 0)
                                <button
                                    class="flex-1 bg-white dark:bg-gray-800 border-2 border-orange-500 dark:border-orange-400 text-orange-500 dark:text-orange-400 py-3 px-6 rounded-xl font-semibold hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors flex items-center justify-center group">
                                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2" />
                                    </svg>
                                    Add to Cart
                                </button>
                                <button
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

                        <!-- Additional Actions -->
                        <div
                            class="flex items-center justify-center space-x-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button
                                class="flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                                </svg>
                                Share
                            </button>
                            <button
                                class="flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                Chat Seller
                            </button>
                        </div>
                    </div>

                    <!-- Product Description -->
                    @if ($product->description)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
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

                    <!-- Product Specifications -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500 dark:text-orange-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Product Specifications
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Brand</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $product->brand ? $product->brand->name : 'No Brand' }}</span>
                            </div>
                            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Category</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $product->category ? $product->category->name : 'No Category' }}</span>
                            </div>
                            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">SKU</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">#{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Status</span>
                                <span class="font-semibold">
                                    @if ($product->is_active)
                                        <span class="text-green-600 dark:text-green-400">Active</span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">Inactive</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Stock</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white">{{ $product->stock_quantity }}
                                    pcs</span>
                            </div>
                            <div class="flex justify-between py-3">
                                <span class="text-gray-600 dark:text-gray-400">Weight</span>
                                <span class="font-semibold text-gray-900 dark:text-white">1 kg</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products Section -->
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Related Products</h2>
                    <a href="/products"
                        class="text-orange-500 dark:text-orange-400 hover:text-orange-600 dark:hover:text-orange-300 font-medium flex items-center">
                        View All
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        // Get related products from same category
                        $relatedProducts = App\Models\Product::where('category_id', $product->category_id)
                            ->where('id', '!=', $product->id)
                            ->where('is_active', true)
                            ->with(['brand', 'category'])
                            ->limit(4)
                            ->get();
                    @endphp

                    @forelse($relatedProducts as $relatedProduct)
                        <a href="/products/{{ $relatedProduct->slug }}"
                            class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow group">
                            <div
                                class="aspect-square relative bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                                @if ($relatedProduct->first_image)
                                    <img src="{{ Storage::url($relatedProduct->first_image) }}"
                                        alt="{{ $relatedProduct->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                @if ($relatedProduct->on_sale)
                                    <div class="absolute top-2 left-2">
                                        <span
                                            class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">DISKON</span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                    {{ $relatedProduct->name }}</h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if ($relatedProduct->on_sale && $relatedProduct->sale_price)
                                            <span class="text-lg font-bold text-orange-500 dark:text-orange-400">
                                                Rp{{ number_format($relatedProduct->sale_price, 0, ',', '.') }}
                                            </span>
                                            <div class="text-sm text-gray-400 line-through">
                                                Rp{{ number_format($relatedProduct->price, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="text-lg font-bold text-orange-500 dark:text-orange-400">
                                                Rp{{ number_format($relatedProduct->price, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-orange-500 dark:text-orange-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No related products found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Styles and Scripts -->
    <style>
        /* Prevent body scroll when fullscreen is active */
        .modal-open {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
        }

        /* Ensure fullscreen modal covers everything */
        .fullscreen-modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Smooth zoom transitions */
        .zoom-transition {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Custom scrollbar for thumbnails */
        .overflow-x-auto::-webkit-scrollbar {
            height: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 2px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Disable text selection during drag */
        .select-none {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced keyboard navigation for image gallery
            document.addEventListener('keydown', function(e) {
                // Only handle keyboard events when not in an input field
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

                // Get Alpine.js component data
                const imageGallery = document.querySelector('[x-data*="currentImageIndex"]');
                if (!imageGallery) return;

                const alpineData = Alpine.$data(imageGallery);

                // Only handle keys if fullscreen is open or we're on the image gallery
                if (!alpineData.showFullscreen && !imageGallery.contains(e.target)) return;

                switch (e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        if (alpineData.images.length > 1) {
                            alpineData.prevImage();
                        }
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        if (alpineData.images.length > 1) {
                            alpineData.nextImage();
                        }
                        break;
                    case 'Escape':
                        e.preventDefault();
                        if (alpineData.showFullscreen) {
                            alpineData.closeFullscreen();
                        }
                        break;
                    case '+':
                    case '=':
                        e.preventDefault();
                        alpineData.zoomIn();
                        break;
                    case '-':
                        e.preventDefault();
                        alpineData.zoomOut();
                        break;
                    case '0':
                        e.preventDefault();
                        alpineData.resetZoom();
                        break;
                }
            });

            // Prevent context menu on images to avoid interference with zoom
            document.addEventListener('contextmenu', function(e) {
                if (e.target.tagName === 'IMG') {
                    e.preventDefault();
                }
            });

            // Touch zoom for mobile devices
            let touchStartDistance = 0;
            let touchStartZoom = 1;

            document.addEventListener('touchstart', function(e) {
                if (e.touches.length === 2) {
                    const imageGallery = document.querySelector('[x-data*="currentImageIndex"]');
                    if (imageGallery) {
                        const alpineData = Alpine.$data(imageGallery);
                        touchStartDistance = Math.hypot(
                            e.touches[0].pageX - e.touches[1].pageX,
                            e.touches[0].pageY - e.touches[1].pageY
                        );
                        touchStartZoom = alpineData.zoomLevel;
                    }
                }
            });

            document.addEventListener('touchmove', function(e) {
                if (e.touches.length === 2) {
                    e.preventDefault();
                    const imageGallery = document.querySelector('[x-data*="currentImageIndex"]');
                    if (imageGallery) {
                        const alpineData = Alpine.$data(imageGallery);
                        const currentDistance = Math.hypot(
                            e.touches[0].pageX - e.touches[1].pageX,
                            e.touches[0].pageY - e.touches[1].pageY
                        );

                        const scale = currentDistance / touchStartDistance;
                        const newZoom = Math.max(1, Math.min(alpineData.maxZoom, touchStartZoom * scale));

                        alpineData.zoomLevel = newZoom;
                        alpineData.isZoomed = newZoom > 1;

                        if (!alpineData.isZoomed) {
                            alpineData.imagePosition = {
                                x: 0,
                                y: 0
                            };
                        }
                    }
                }
            });
        });
    </script>
</x-layouts.public-app>
