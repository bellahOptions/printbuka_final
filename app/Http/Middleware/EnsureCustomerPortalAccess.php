<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerPortalAccess
{
    /**
     * Allow guests and customers, but keep staff/admin accounts out of customer flows.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (($user->role ?? 'customer') === 'customer') {
            return $next($request);
        }

        if ($user->hasAdminAccess()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('warning', 'Staff accounts can only access the admin portal.');
        }

        abort(403, 'Staff accounts cannot access customer pages.');
    }
}

