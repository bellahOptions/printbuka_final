<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfUserAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return Auth::user()?->hasAdminAccess()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }

        return $next($request);
    }
}
