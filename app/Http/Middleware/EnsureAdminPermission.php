<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission = 'admin.view'): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAdminAccess() || ! $user->canAdmin($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
