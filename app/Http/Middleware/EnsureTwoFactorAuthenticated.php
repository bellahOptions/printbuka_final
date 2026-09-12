<?php

namespace App\Http\Middleware;

use App\Support\SiteSettings;
use App\Support\TwoFactorTrust;
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

        // Always allow the 2FA/OTP routes themselves through
        if ($request->routeIs('admin.two-factor.*') || $request->routeIs('admin.otp.*')) {
            return $next($request);
        }

        // A device the staff member chose to trust skips re-challenging for
        // the duration they picked at their last verification.
        if (! session('staff_2fa_verified') && TwoFactorTrust::check($request, $user)) {
            session(['staff_2fa_verified' => true]);
        }

        // TOTP already confirmed: unchanged, regardless of the OTP toggle
        if ($user->hasTwoFactorEnabled()) {
            if (! session('staff_2fa_verified')) {
                $request->session()->put('url.intended', $request->url());
                return redirect()->route('admin.two-factor.challenge');
            }

            return $next($request);
        }

        // No TOTP confirmed. When the OTP system is disabled, restore the
        // original behavior of forcing TOTP enrollment.
        if (! SiteSettings::otpEnabled()) {
            return redirect()->route('admin.two-factor.setup');
        }

        // OTP enabled: verify by emailed code instead of forcing TOTP setup.
        if (! session('staff_2fa_verified')) {
            $request->session()->put('url.intended', $request->url());
            return redirect()->route('admin.otp.challenge');
        }

        return $next($request);
    }
}
