<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        // Baris ini sudah benar, secara eksplisit logout dari guard 'web'
        Auth::guard('web')->logout();

        // Menghapus dan meregenerasi sesi untuk keamanan
        Session::invalidate();
        Session::regenerateToken();

        // Mengarahkan kembali ke halaman utama
        return redirect('/');
    }
}
