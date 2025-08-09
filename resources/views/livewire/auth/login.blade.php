<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Enums\UserRole;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Mount the component and set intended URL if provided
     */
    public function mount(): void
    {
        // Cek apakah ada parameter intended di URL
        if (request('intended')) {
            session()->put('url.intended', request('intended'));
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // PERBAIKAN: Cek apakah user adalah customer
        if ($user->role !== UserRole::Customer) {
            // Jika bukan customer, logout dan tampilkan pesan error
            Auth::logout();

            // PERBAIKAN: Jangan regenerate session di sini untuk menghindari page expired
            // Session::regenerate(); // HAPUS BARIS INI

            RateLimiter::hit($this->throttleKey());

            // PERBAIKAN: Reset form dan refresh component
            $this->reset(['email', 'password', 'remember']);

            // PERBAIKAN: Dispatch browser event untuk refresh halaman
            $this->dispatch('refresh-page');

            throw ValidationException::withMessages([
                'form' => 'This login form is only for customers. Please use the appropriate login page.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Customer berhasil login, redirect ke intended URL atau home
        $this->redirectIntended(default: route('home', absolute: false), navigate: true);
    }

    /**
     * Check if user has access to the intended URL based on role
     */
    private function checkRoleAccess($user, string $url): bool
    {
        // Ekstrak path dari URL
        $path = parse_url($url, PHP_URL_PATH);

        // Definisikan pola URL yang memerlukan role tertentu
        $rolePatterns = [
            'customer' => ['/dashboard', '/profile', '/orders'], // Area customer
            'admin' => ['/admin'], // Area Filament admin
        ];

        // Cek apakah URL memerlukan role tertentu
        foreach ($rolePatterns as $requiredRole => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($path, $pattern) === 0) {
                    // URL memerlukan role tertentu, cek apakah user memiliki role tersebut
                    $requiredRoleEnum = UserRole::tryFrom($requiredRole);
                    return $user->role === $requiredRoleEnum;
                }
            }
        }

        // URL tidak memerlukan role khusus (misal: home, about, contact)
        return true;
    }

    /**
     * Redirect to role-based home page
     */
    private function redirectToRoleBasedHome($user, string $errorMessage = null): void
    {
        if ($user->role === UserRole::Admin) {
            // Admin redirect ke Filament admin panel
            if ($errorMessage) {
                session()->flash('error', $errorMessage);
            }
            $this->redirect('/admin', navigate: true);
        } elseif ($user->role === UserRole::Customer) {
            // Customer redirect ke home
            if ($errorMessage) {
                session()->flash('error', $errorMessage);
            }
            $this->redirect(route('home'), navigate: true);
        } else {
            // Role tidak dikenali, logout dan redirect ke login
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            session()->flash('error', 'Invalid user role.');
            $this->redirect(route('login'), navigate: true);
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

    <!-- =================================================================== -->
    <!-- BLOK NOTIFIKASI YANG DIPERBARUI -->
    <!-- =================================================================== -->

    <!-- Menampilkan pesan error dari middleware -->
    @if (session('error'))
        <div class="flex items-center p-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-zinc-900 dark:text-red-400"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 0 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Menampilkan pesan sukses dari registrasi -->
    @if (session('status'))
        <div class="flex items-center p-4 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-zinc-900 dark:text-green-400"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 0 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @error('form')
        <div class="flex items-center p-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-zinc-900 dark:text-red-400"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 0 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">{{ $message }}</span>
            </div>
        </div>
    @enderror

    <!-- =================================================================== -->
    <!-- AKHIR BLOK NOTIFIKASI -->
    <!-- =================================================================== -->

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Email address')" type="email" required autofocus
            autocomplete="email" placeholder="email@example.com" />

        <!-- Password -->
        <div class="relative">
            <flux:input wire:model="password" :label="__('Password')" type="password" required
                autocomplete="current-password" :placeholder="__('Password')" viewable />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif

    <!-- PERBAIKAN: JavaScript untuk menangani refresh otomatis -->
    {{-- <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('refresh-page', () => {
                // Refresh halaman setelah delay singkat agar pesan error sempat ditampilkan
                setTimeout(() => {
                    window.location.reload();
                }, 500); // 0,5 detik delay
            });
        });
    </script> --}}
</div>
