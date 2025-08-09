<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // PERBAIKAN: Redirect berdasarkan role user
                if ($user->role === UserRole::Admin) {
                    // Admin redirect ke Filament admin panel
                    return redirect('/admin');
                } elseif ($user->role === UserRole::Customer) {
                    // Customer redirect ke home atau intended URL
                    return redirect()->intended('/');
                } else {
                    // Role tidak dikenali, logout dan redirect ke login
                    Auth::guard($guard)->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect('/login')->with('error', 'Invalid user role.');
                }
            }
        }

        return $next($request);
    }
}
