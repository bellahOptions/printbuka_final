<?php

use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\EnforceSiteMaintenance;
use App\Http\Middleware\EnsureAdminPermission;
use App\Http\Middleware\EnsureCustomerPortalAccess;
use App\Http\Middleware\EnsureKycApproved;
use App\Http\Middleware\EnsureStaffIsActive;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\EnsureUserEmailIsVerified;
use App\Http\Middleware\EnsureUserIsAuthenticated;
use App\Http\Middleware\LogStaffActivity;
use App\Http\Middleware\RedirectIfUserAuthenticated;
use App\Http\Middleware\VerifyTurnstile;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')->group(base_path('routes/admin-auth.php'));
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            AddSecurityHeaders::class,
            EnforceSiteMaintenance::class,
            VerifyTurnstile::class,
            LogStaffActivity::class,
        ]);

        $middleware->alias([
            // Web
            'user.auth'        => EnsureUserIsAuthenticated::class,
            'user.guest'       => RedirectIfUserAuthenticated::class,
            'user.verified'    => EnsureUserEmailIsVerified::class,
            'customer.portal'  => EnsureCustomerPortalAccess::class,
            'admin.permission' => EnsureAdminPermission::class,
            'super.admin'      => EnsureSuperAdmin::class,
            'admin.activity'   => LogStaffActivity::class,
            'staff.2fa'        => \App\Http\Middleware\EnsureTwoFactorAuthenticated::class,
            'staff.kyc'        => EnsureKycApproved::class,
            // Mobile API
            'abilities'        => CheckAbilities::class,
            'ability'          => CheckForAnyAbility::class,
            'staff.active'     => EnsureStaffIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
