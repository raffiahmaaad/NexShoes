<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole; // Pastikan Enum diimpor

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  // Ini adalah string dari rute, misal: 'customer'
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // --- INI LOGIKA BARU YANG LEBIH KUAT ---

        // 1. Coba ubah string 'customer' dari rute menjadi objek UserRole Enum.
        $requiredRole = UserRole::tryFrom($role);

        // 2. Bandingkan objek Enum secara langsung.
        // Cek apakah konversi berhasil DAN role user sama dengan role yang dibutuhkan.
        if ($requiredRole && $user->role === $requiredRole) {
            // Jika sama, lanjutkan ke dashboard.
            return $next($request);
        }

        // --- AKHIR LOGIKA BARU ---


        // Jika tidak cocok, logout dan redirect.
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('error', 'You do not have access to this page.');
    }
}
