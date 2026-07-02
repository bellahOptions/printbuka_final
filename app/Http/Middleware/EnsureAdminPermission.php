<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission = 'admin.view'): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // Access restriction takes priority — log out and redirect with a clear message
        if ($user->isAccessRestricted()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->with('status_error', 'Your access has been restricted by an administrator. Please contact your manager.');
        }

        if (! $user->hasAdminAccess() || ! $user->canAdmin($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
