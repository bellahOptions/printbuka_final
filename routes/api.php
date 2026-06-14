<?php

use App\Http\Controllers\Api\StaffAuthController;
use App\Http\Controllers\Api\StaffDeviceController;
use App\Http\Controllers\Api\StaffNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Staff Mobile API Routes
|--------------------------------------------------------------------------
| Consumed by the NativePHP staff mobile app.
| Auth: Laravel Sanctum (stateless token — no CSRF, no sessions).
| All routes are prefixed with /api automatically by bootstrap/app.php.
|--------------------------------------------------------------------------
*/

// Public — no token needed
Route::prefix('staff')->group(function () {
    Route::post('/login', [StaffAuthController::class, 'login']);
});

// Protected — requires a valid Sanctum token with the 'staff' ability
Route::prefix('staff')
    ->middleware(['auth:sanctum', 'abilities:staff', 'staff.active'])
    ->group(function () {

        // Auth
        Route::get('/me', [StaffAuthController::class, 'me']);
        Route::post('/logout', [StaffAuthController::class, 'logout']);

        // Device registration (FCM token)
        Route::post('/devices', [StaffDeviceController::class, 'store']);
        Route::delete('/devices', [StaffDeviceController::class, 'destroy']);

        // Notification inbox
        Route::get('/notifications', [StaffNotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [StaffNotificationController::class, 'unreadCount']);
        Route::post('/notifications/mark-all-read', [StaffNotificationController::class, 'markAllRead']);
        Route::delete('/notifications/clear-read', [StaffNotificationController::class, 'clearRead']);
        Route::post('/notifications/{id}/read', [StaffNotificationController::class, 'markRead']);
        Route::delete('/notifications/{id}', [StaffNotificationController::class, 'destroy']);
    });
