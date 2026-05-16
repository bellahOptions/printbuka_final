<?php

namespace App\Http\Middleware;

use App\Support\Turnstile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTurnstile
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldVerify($request)) {
            Turnstile::verify($request);
        }

        return $next($request);
    }

    private function shouldVerify(Request $request): bool
    {
        if (! Turnstile::enabled() || $request->isMethodSafe()) {
            return false;
        }

        if ($request->routeIs('admin.*') || $request->routeIs('livewire.*') || $request->routeIs('logout')) {
            return false;
        }

        return true;
    }
}
