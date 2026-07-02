<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycApproved
{
    private const EXEMPT_ROLES = ['super_admin', 'managing_director', 'hr'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role === 'customer') {
            return $next($request);
        }

        if (\in_array($user->role, self::EXEMPT_ROLES, true)) {
            return $next($request);
        }

        // Allow 2FA, profile, and logout routes through
        if ($request->routeIs('admin.two-factor.*', 'admin.staff.profile.*', 'admin.staff.kyc-complete', 'admin.logout')) {
            return $next($request);
        }

        $kycStatus = $user->staffProfile?->kyc_status ?? 'pending';

        if ($kycStatus !== 'approved') {
            $message = match ($kycStatus) {
                'correction_requested' => 'Your KYC has been returned for corrections. Please update your profile to continue.',
                default                => 'Please complete your KYC before accessing the system. Fill in your bio-data and await HR approval.',
            };

            return redirect()
                ->route('admin.staff.profile.show', $user)
                ->with('status_error', $message);
        }

        return $next($request);
    }
}
