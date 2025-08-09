<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="arrow-uturn-left" href="/" target="_blank">
                {{ __('Back to Home') }}
            </flux:navlist.item>
        </flux:navlist>

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:button variant="ghost" class="!p-0 !bg-transparent hover:!bg-zinc-100 dark:hover:!bg-zinc-800">
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors duration-200">
                    <!-- Custom Avatar -->
                    <div class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                        @if (auth()->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar))
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                class="h-full w-full object-cover object-center">
                        @else
                            <div
                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 text-white font-semibold text-xs">
                                {{ auth()->user()->initials() }}
                            </div>
                        @endif
                    </div>

                    <!-- Name and Icon -->
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <svg class="h-4 w-4 text-zinc-500 dark:text-zinc-400 shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                        </svg>
                    </div>
                </div>
            </flux:button>

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span
                                class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
                                @if (auth()->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar))
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                        class="h-full w-full object-cover object-center">
                                @else
                                    <span
                                        class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 text-white font-semibold text-xs">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                @endif
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span
                                    class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:button variant="ghost" class="!p-0 !bg-transparent hover:!bg-zinc-100 dark:hover:!bg-zinc-800">
                <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg transition-colors duration-200">
                    <!-- Custom Avatar -->
                    <div class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                        @if (auth()->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar))
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                class="h-full w-full object-cover object-center">
                        @else
                            <div
                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 text-white font-semibold text-xs">
                                {{ auth()->user()->initials() }}
                            </div>
                        @endif
                    </div>

                    <!-- Chevron Icon -->
                    <svg class="h-4 w-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </flux:button>

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span
                                class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
                                @if (auth()->user()->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar))
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                        class="h-full w-full object-cover object-center">
                                @else
                                    <span
                                        class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 text-white font-semibold text-xs">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                @endif
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span
                                    class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
