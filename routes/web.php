<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Rute Publik (Bisa diakses siapa saja)
|--------------------------------------------------------------------------
|
| Rute-rute ini tidak memerlukan login.
|
*/
Route::view('/', 'pages.index')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');

Route::get('/products', function () {
    return view('pages.products.index');
});

Route::get('/products/{slug}', function ($slug) {
    return view('pages.products.[slug]', compact('slug'));
});


/*
|--------------------------------------------------------------------------
| Rute Otentikasi Customer (Guard: 'web')
|--------------------------------------------------------------------------
|
| Semua rute di dalam grup ini sekarang dilindungi oleh middleware 'auth',
| 'verified', dan 'role:customer'. Ini adalah area khusus untuk customer.
|
*/
// 👇 Middleware 'role:customer' ditambahkan di sini
Route::middleware(['auth', 'verified', 'role:customer'])->group(function () {
    // Dashboard Customer
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Pengaturan Akun Customer
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', '/settings/profile'); // Redirect dari /settings ke /settings/profile
        Volt::route('profile', 'settings.profile')->name('profile');
        Volt::route('password', 'settings.password')->name('password');
        Volt::route('appearance', 'settings.appearance')->name('appearance');
    });

    // Anda bisa menambahkan rute customer lain yang butuh login di sini...
});


/*
|--------------------------------------------------------------------------
| File Rute Otentikasi Bawaan Laravel
|--------------------------------------------------------------------------
|
| File ini berisi rute untuk login, register, lupa password, dll.
| untuk guard 'web' (customer). Jangan dihapus.
|
*/
require __DIR__.'/auth.php';
