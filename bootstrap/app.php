<?php

use App\Http\Middleware\EnforceSiteMaintenance;
use App\Http\Middleware\EnsureAdminPermission;
use App\Http\Middleware\EnsureCustomerPortalAccess;
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

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')->group(base_path('routes/admin-auth.php'));
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            EnforceSiteMaintenance::class,
            VerifyTurnstile::class,
            LogStaffActivity::class,
        ]);

        $middleware->alias([
            'user.auth' => EnsureUserIsAuthenticated::class,
            'user.guest' => RedirectIfUserAuthenticated::class,
            'user.verified' => EnsureUserEmailIsVerified::class,
            'customer.portal' => EnsureCustomerPortalAccess::class,
            'admin.permission' => EnsureAdminPermission::class,
            'super.admin' => EnsureSuperAdmin::class,
            'admin.activity' => LogStaffActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
