<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // geolocation is scoped to same-origin (not blocked outright) — staff
        // attendance clock-in/out requires it to verify staff are on site.
        // Camera/microphone stay fully denied: nothing in this app uses the
        // getUserMedia() JS API (selfie capture goes through the plain HTML
        // file-input camera picker instead, which this policy doesn't affect).
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
