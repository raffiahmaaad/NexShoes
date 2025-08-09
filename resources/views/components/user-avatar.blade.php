@props(['user', 'size' => 'md', 'class' => ''])

@php
    $sizeClasses = match ($size) {
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-lg',
        '2xl' => 'w-20 h-20 text-xl',
        default => 'w-10 h-10 text-sm',
    };
@endphp

@if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar))
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
        class="{{ $sizeClasses }} rounded-lg object-cover {{ $class }}">
@else
    <div
        class="{{ $sizeClasses }} bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-semibold {{ $class }}">
        {{ $user->initials() }}
    </div>
@endif
