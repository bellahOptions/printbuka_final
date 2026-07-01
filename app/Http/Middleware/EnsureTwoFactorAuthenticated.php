<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only enforce for authenticated non-customer staff
        if (! $user || $user->role === 'customer') {
            return $next($request);
        }

        // Always allow the 2FA routes themselves through
        if ($request->routeIs('admin.two-factor.*')) {
            return $next($request);
        }

        // 2FA not set up yet — force setup
        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.two-factor.setup');
        }

        // 2FA set up but not verified this session — redirect to challenge
        if (! session('staff_2fa_verified')) {
            $request->session()->put('url.intended', $request->url());
            return redirect()->route('admin.two-factor.challenge');
        }

        return $next($request);
    }
}
