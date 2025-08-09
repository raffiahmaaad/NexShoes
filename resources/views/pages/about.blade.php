<?php

use function Livewire\Volt\{state};

?>

@volt
    <div>
        <x-layouts.public-app>
            <div
                class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">

                <!-- Hero Section -->
                <section class="relative py-20 lg:py-32 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
                    <div class="container mx-auto px-4 relative">
                        <div class="max-w-4xl mx-auto text-center">
                            <div
                                class="inline-flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900/30 rounded-full text-blue-700 dark:text-blue-300 text-sm font-medium mb-6">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Terpercaya sejak 2008
                            </div>
                            <h1
                                class="text-5xl lg:text-7xl font-bold mb-6 bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-300 dark:to-purple-300 bg-clip-text text-transparent leading-tight">
                                About NexShoes
                            </h1>
                            <p
                                class="text-xl lg:text-2xl text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl mx-auto">
                                Kami adalah toko sepatu yang telah berpengalaman lebih dari 15 tahun dalam menyediakan
                                sepatu berkualitas tinggi dengan harga terjangkau. Komitmen kami adalah memberikan
                                pelayanan terbaik kepada setiap pelanggan.
                            </p>
                        </div>
                    </div>

                    <!-- Static Floating Elements -->
                    <div class="absolute top-20 left-10 w-20 h-20 bg-blue-500/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-20 right-10 w-32 h-32 bg-purple-500/10 rounded-full blur-xl"></div>
                </section>

                <!-- Story Section -->
                <section class="py-20 lg:py-28">
                    <div class="container mx-auto px-4">
                        <div class="grid lg:grid-cols-2 gap-16 items-center">
                            <div class="space-y-8">
                                <div class="space-y-6">
                                    <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight">
                                        Kisah <span
                                            class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Perjalanan</span>
                                        Kami
                                    </h2>
                                    <div class="w-20 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></div>
                                </div>

                                <div class="space-y-6 text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                                    <p>
                                        Dimulai dari sebuah toko kecil di Jakarta pada tahun 2008, kami memiliki visi untuk
                                        menyediakan sepatu berkualitas dengan harga yang terjangkau untuk semua kalangan.
                                    </p>
                                    <p>
                                        Kepuasan pelanggan adalah prioritas utama kami, dan kami berkomitmen untuk terus
                                        memberikan pelayanan terbaik serta produk-produk berkualitas yang memenuhi kebutuhan
                                        dan gaya hidup modern.
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-gray-600 dark:text-gray-300 font-medium">Kualitas Premium</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-gray-600 dark:text-gray-300 font-medium">Harga Terjangkau</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                        <span class="text-gray-600 dark:text-gray-300 font-medium">Pelayanan Terbaik</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics Card - Simplified -->
                            <div class="relative">
                                <div
                                    class="bg-white/90 dark:bg-gray-800/90 rounded-3xl shadow-xl p-8 lg:p-10 border border-gray-200/50 dark:border-gray-700/50">
                                    <div class="text-center mb-8">
                                        <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                            Statistik Kami</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Pencapaian yang membanggakan</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-8">
                                        <div class="text-center group">
                                            <div
                                                class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-3">
                                                15+</div>
                                            <div class="text-gray-600 dark:text-gray-400 font-medium">Tahun Pengalaman</div>
                                        </div>

                                        <div class="text-center group">
                                            <div
                                                class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent mb-3">
                                                10K+</div>
                                            <div class="text-gray-600 dark:text-gray-400 font-medium">Pelanggan Puas</div>
                                        </div>

                                        <div class="text-center group">
                                            <div
                                                class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">
                                                500+</div>
                                            <div class="text-gray-600 dark:text-gray-400 font-medium">Produk</div>
                                        </div>

                                        <div class="text-center group">
                                            <div
                                                class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-3">
                                                100+</div>
                                            <div class="text-gray-600 dark:text-gray-400 font-medium">Rekanan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Values Section -->
                <section
                    class="py-20 lg:py-28 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700">
                    <div class="container mx-auto px-4">
                        <div class="text-center mb-16">
                            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                                Nilai-Nilai <span
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Kami</span>
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                                Prinsip-prinsip yang menjadi fondasi dalam setiap langkah perjalanan bisnis kami
                            </p>
                        </div>

                        <div class="grid md:grid-cols-3 gap-8">
                            <div
                                class="group bg-white/80 dark:bg-gray-800/80 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-100 border border-gray-200/50 dark:border-gray-700/50">
                                <div
                                    class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Kualitas Terjamin</h3>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Setiap produk melalui kontrol kualitas ketat untuk memastikan standar terbaik sampai ke
                                    tangan pelanggan.
                                </p>
                            </div>

                            <div
                                class="group bg-white/80 dark:bg-gray-800/80 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-100 border border-gray-200/50 dark:border-gray-700/50">
                                <div
                                    class="w-16 h-16 bg-gradient-to-r from-green-500 to-blue-500 rounded-2xl flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Pelayanan Prima</h3>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Tim customer service yang responsif dan ramah siap membantu kebutuhan sepatu impian
                                    Anda.
                                </p>
                            </div>

                            <div
                                class="group bg-white/80 dark:bg-gray-800/80 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-100 border border-gray-200/50 dark:border-gray-700/50">
                                <div
                                    class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Inovasi Berkelanjutan</h3>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Selalu menghadirkan tren terkini dan teknologi sepatu terdepan untuk kenyamanan
                                    maksimal.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Team Section -->
                <section class="py-20 lg:py-28 bg-white dark:bg-gray-900">
                    <div class="container mx-auto px-4">
                        <div class="text-center mb-16">
                            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                                Tim <span
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Terbaik</span>
                                Kami
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                                Dibalik kesuksesan NexShoes, terdapat tim profesional yang berdedikasi tinggi untuk
                                memberikan yang terbaik
                            </p>
                        </div>

                        <div class="grid gap-8 lg:gap-12 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 justify-center">
                            <!-- Team Member 1 -->
                            <div class="group text-center">
                                <div class="relative mx-auto mb-6 w-40 h-40 overflow-hidden rounded-3xl shadow-lg">
                                    <img class="w-full h-full object-cover"
                                        src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png"
                                        alt="Bonnie Green Avatar" loading="lazy">
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Bonnie Green</h3>
                                <p class="text-blue-600 dark:text-blue-400 font-semibold mb-4">CEO/Co-founder</p>
                                <div class="flex justify-center space-x-3">
                                    <a href="#"
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="#"
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path
                                                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Team Member 2 -->
                            <div class="group text-center">
                                <div class="relative mx-auto mb-6 w-40 h-40 overflow-hidden rounded-3xl shadow-lg">
                                    <img class="w-full h-full object-cover"
                                        src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/helene-engels.png"
                                        alt="Helene Engels Avatar" loading="lazy">
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Helene Engels</h3>
                                <p class="text-blue-600 dark:text-blue-400 font-semibold mb-4">CTO/Co-founder</p>
                                <div class="flex justify-center space-x-3">
                                    <a href="#"
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="#"
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path
                                                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Team Member 3 -->
                            <div class="group text-center">
                                <div class="relative mx-auto mb-6 w-40 h-40 overflow-hidden rounded-3xl shadow-lg">
                                    <img class="w-full h-full object-cover"
                                        src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png"
                                        alt="Jese Leos Avatar" loading="lazy">
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Jese Leos</h3>
                                <p class="text-blue-600 dark:text-blue-400 font-semibold mb-4">SEO & Marketing</p>
                                <div class="flex justify-center space-x-3">
                                    <a href="#"
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="#"
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path
                                                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Team Member 4 -->
                            <div class="group text-center">
                                <div class="relative mx-auto mb-6 w-40 h-40 overflow-hidden rounded-3xl shadow-lg">
                                    <img class="w-full h-full object-cover"
                                        src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/robert-brown.png"
                                        alt="Robert Brown Avatar" loading="lazy">
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Robert Brown</h3>
                                <p class="text-blue-600 dark:text-blue-400 font-semibold mb-4">Product Designer</p>
                                <div class="flex justify-center space-x-3">
                                    <a href="#"
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="#"
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path
                                                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CTA Section -->
                <section
                    class="py-20 lg:py-28 bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute inset-0">
                        <div class="absolute top-0 left-0 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 right-0 w-60 h-60 bg-white/5 rounded-full blur-2xl"></div>
                        <div
                            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-white/5 rounded-full blur-2xl">
                        </div>
                    </div>

                    <div class="container mx-auto px-4 relative z-10">
                        <div class="max-w-4xl mx-auto text-center">
                            <h2 class="text-4xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                                Siap Bergabung dengan <span class="text-yellow-300">Keluarga</span> NexShoes?
                            </h2>
                            <p class="text-xl lg:text-2xl text-blue-100 mb-10 leading-relaxed">
                                Rasakan pengalaman berbelanja sepatu yang tak terlupakan bersama ribuan pelanggan puas
                                lainnya
                            </p>

                            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                                <a href="/products"
                                    class="group inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-2xl hover:bg-blue-50 transition-all duration-100 shadow-xl">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Jelajahi Koleksi
                                </a>

                                <a href="/contact"
                                    class="group inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-2xl hover:bg-white hover:text-blue-600 transition-all duration-100">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                        </path>
                                    </svg>
                                    Hubungi Kami
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </x-layouts.public-app>
    </div>
@endvolt
