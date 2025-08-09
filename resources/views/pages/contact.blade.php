<?php

use function Livewire\Volt\{state, rules};

state([
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
    'success' => false,
]);

rules([
    'name' => 'required|min:3',
    'email' => 'required|email',
    'subject' => 'required|min:5',
    'message' => 'required|min:10',
]);

$submit = function () {
    $this->validate();

    // Logic untuk mengirim email atau menyimpan ke database
    // Mail::to('admin@nexshoes.com')->send(new ContactMessage($this->name, $this->email, $this->subject, $this->message));

    $this->success = true;
    $this->reset(['name', 'email', 'subject', 'message']);

    session()->flash('message', 'Your message has been sent successfully! We will get back to you soon.');
};

?>
@volt
    <div>
        <x-layouts.public-app>
            <!-- Hero Section -->
            <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-purple-800 overflow-hidden">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="relative container mx-auto px-4 py-24 lg:py-32">
                    <div class="text-center">
                        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                            Get In <span class="text-blue-300">Touch</span>
                        </h1>
                        <p class="text-xl text-blue-100 max-w-3xl mx-auto leading-relaxed">
                            Have questions about our shoes? Need support? Or want to share feedback?
                            We'd love to hear from you.
                        </p>
                    </div>
                </div>
                <!-- Decorative Elements -->
                <div class="absolute top-0 left-0 w-72 h-72 bg-blue-500/10 rounded-full -translate-x-1/2 -translate-y-1/2">
                </div>
                <div
                    class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500/10 rounded-full translate-x-1/2 translate-y-1/2">
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-gray-50 dark:bg-gray-900 py-16 lg:py-24">
                <div class="container mx-auto px-4">
                    <div class="max-w-6xl mx-auto">
                        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">
                            <!-- Contact Information -->
                            <div class="space-y-8">
                                <div>
                                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                                        Let's Start a Conversation
                                    </h2>
                                    <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                                        We're here to help you find the perfect shoes and provide excellent customer
                                        service.
                                        Reach out to us through any of the channels below.
                                    </p>
                                </div>

                                <!-- Contact Cards -->
                                <div class="space-y-6">
                                    <!-- Address -->
                                    <div
                                        class="flex items-start space-x-4 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-100">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Visit Our
                                                Store</h3>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                Jl. Sepatu Raya No. 123<br>
                                                Jakarta Selatan, 12345<br>
                                                Indonesia
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div
                                        class="flex items-start space-x-4 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-100">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Call Us
                                            </h3>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                +62 21 1234 5678<br>
                                                <span class="text-sm">Mon - Fri: 9:00 AM - 6:00 PM</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div
                                        class="flex items-start space-x-4 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-100">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Email Us
                                            </h3>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                info@nexshoes.com<br>
                                                <span class="text-sm">We'll respond within 24 hours</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Media -->
                                <div class="pt-8">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Follow Us</h3>
                                    <div class="flex space-x-4">
                                        <a href="#"
                                            class="w-12 h-12 bg-blue-600 hover:bg-blue-700 rounded-xl flex items-center justify-center transition-colors duration-100">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                            </svg>
                                        </a>
                                        <a href="#"
                                            class="w-12 h-12 bg-blue-800 hover:bg-blue-900 rounded-xl flex items-center justify-center transition-colors duration-100">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                            </svg>
                                        </a>
                                        <a href="#"
                                            class="w-12 h-12 bg-pink-600 hover:bg-pink-700 rounded-xl flex items-center justify-center transition-colors duration-100">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.083.346-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.748-1.378 0 0-.599 2.282-.744 2.840-.282 1.084-1.064 2.456-1.549 3.235C9.584 23.815 10.77 24.001 12.017 24.001c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Form -->
                            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 lg:p-10">
                                <div class="mb-8">
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Send us a Message</h2>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Fill out the form below and we'll get back to you as soon as possible.
                                    </p>
                                </div>

                                @if (session('message'))
                                    <div
                                        class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                                    {{ session('message') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <form wire:submit="submit" class="space-y-6">
                                    <!-- Name Field -->
                                    <div>
                                        <label for="name"
                                            class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                            Full Name
                                        </label>
                                        <input type="text" id="name" wire:model="name"
                                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-100"
                                            placeholder="Enter your full name">
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Email Field -->
                                    <div>
                                        <label for="email"
                                            class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                            Email Address
                                        </label>
                                        <input type="email" id="email" wire:model="email"
                                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-100"
                                            placeholder="your.email@example.com">
                                        @error('email')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Subject Field -->
                                    <div>
                                        <label for="subject"
                                            class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                            Subject
                                        </label>
                                        <input type="text" id="subject" wire:model="subject"
                                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-100"
                                            placeholder="How can we help you?">
                                        @error('subject')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Message Field -->
                                    <div>
                                        <label for="message"
                                            class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                            Message
                                        </label>
                                        <textarea id="message" wire:model="message" rows="5"
                                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-100 resize-none"
                                            placeholder="Tell us more about your inquiry..."></textarea>
                                        @error('message')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-100 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-lg hover:shadow-xl"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove>Send Message</span>
                                        <span wire:loading class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                                Sending...
                                            </svg>

                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="bg-white dark:bg-gray-800 py-16 lg:py-24">
                <div class="container mx-auto px-4">
                    <div class="max-w-4xl mx-auto">
                        <div class="text-center mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                                Frequently Asked Questions
                            </h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                Quick answers to common questions about NexShoes
                            </p>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    What is your return policy?
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    We offer a 30-day return policy for all unworn shoes in original packaging.
                                    Returns are free for defective items.
                                </p>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    How long does shipping take?
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Standard shipping takes 3-5 business days within Indonesia.
                                    Express shipping is available for next-day delivery.
                                </p>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    Do you offer international shipping?
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Yes, we ship internationally to over 50 countries.
                                    Shipping costs and delivery times vary by destination.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-layouts.public-app>
    </div>
@endvolt
