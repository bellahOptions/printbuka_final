<?php

namespace App\Http\Middleware;

use App\Support\SiteSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSiteMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! SiteSettings::maintenanceEnabled() || $this->shouldBypassMaintenance($request)) {
            return $next($request);
        }

        $settings = SiteSettings::all();
        $message = $settings['maintenance_message'] ?: 'We are making a few improvements. Please check back shortly.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 503);
        }

        return response()->view('errors.503', [
            'message' => $message,
            'siteSettings' => $settings,
        ], 503);
    }

    private function shouldBypassMaintenance(Request $request): bool
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            return true;
        }

        if ($request->is('login', 'staff/login', 'staff/register', 'logout', 'email/verify', 'email/verify/*', 'email/verification-notification', 'forgot-password', 'reset-password', 'reset-password/*')) {
            return true;
        }

        if ($request->routeIs('login', 'login.store', 'staff.login', 'staff.login.store', 'staff.register', 'staff.register.store', 'logout', 'verification.*', 'password.*')) {
            return true;
        }

        return (bool) $request->user()?->hasAdminAccess();
    }
}
