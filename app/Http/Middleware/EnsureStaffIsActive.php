<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks API requests from staff whose account has been suspended or terminated
 * after they already received a Sanctum token. Runs on every authenticated
 * API request so revocation takes effect immediately without waiting for
 * the token to expire.
 */
class EnsureStaffIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->hasAdminAccess()) {
            // Revoke all mobile tokens so the app is forced to re-authenticate
            $user->tokens()->where('name', 'mobile')->delete();

            return response()->json([
                'message' => 'Your staff access has been revoked. Please contact HR.',
            ], 403);
        }

        if (in_array($user->employment_status, ['suspended', 'terminated'], true)) {
            $user->tokens()->where('name', 'mobile')->delete();

            return response()->json([
                'message' => 'Your account is '.$user->employment_status.'. Please contact HR.',
                'status'  => $user->employment_status,
            ], 403);
        }

        return $next($request);
    }
}
