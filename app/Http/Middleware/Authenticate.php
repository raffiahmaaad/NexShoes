<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Simpan URL yang sedang dikunjungi sebagai intended URL
            session()->put('url.intended', $request->fullUrl());

            return route('login');
        }

        return null;
    }
}
