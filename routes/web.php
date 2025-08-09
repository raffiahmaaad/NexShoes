<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;

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
})->name('products.index');

Route::get('/products/{slug}', function ($slug) {
    return view('pages.products.[slug]', compact('slug'));
})->name('products.show');


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

    // Cart Routes - Hanya untuk customer yang sudah login
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'addItem'])->name('add');
        Route::patch('/update/{id}', [CartController::class, 'updateQuantity'])->name('update');
        Route::delete('/remove/{id}', [CartController::class, 'removeItem'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    });

    // Checkout Routes - Hanya untuk customer yang sudah login
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
        Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
    });

    // Route untuk cart dan checkout tanpa prefix (untuk backward compatibility)
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

    // Order History - untuk melihat riwayat pesanan
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
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
