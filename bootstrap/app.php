<?php

use App\Http\Middleware\EnsureUserIsAuthenticated;
use App\Http\Middleware\EnsureAdminPermission;
use App\Http\Middleware\LogStaffActivity;
use App\Http\Middleware\RedirectIfUserAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'user.auth' => EnsureUserIsAuthenticated::class,
            'user.guest' => RedirectIfUserAuthenticated::class,
            'admin.permission' => EnsureAdminPermission::class,
            'admin.activity' => LogStaffActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
