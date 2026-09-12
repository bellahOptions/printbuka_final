<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\OtpController;
use App\Http\Controllers\Admin\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::middleware('user.guest')->group(function (): void {
    Route::get('/staff/login', [AuthController::class, 'showStaffLogin'])->name('staff.login');
    Route::post('/staff/login', [AuthController::class, 'staffLogin'])
        ->middleware('throttle:8,1')
        ->name('staff.login.store');
    Route::get('/staff/register', [AuthController::class, 'showStaffRegister'])->name('staff.register');
    Route::post('/staff/register', [AuthController::class, 'staffRegister'])
        ->middleware('throttle:5,1')
        ->name('staff.register.store');
});

// 2FA routes — require auth + verified but NOT the 2FA middleware itself
Route::middleware(['user.auth', 'user.verified', 'staff.active'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/two-factor/setup', [TwoFactorController::class, 'showSetup'])->name('two-factor.setup');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::get('/two-factor/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes');
    Route::post('/two-factor/recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes.regenerate');
    Route::get('/two-factor/challenge', [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge');
    Route::post('/two-factor/challenge', [TwoFactorController::class, 'verifyChallenge'])->name('two-factor.verify');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::get('/two-factor/trusted-devices', [TwoFactorController::class, 'showTrustedDevices'])->name('two-factor.trusted-devices');
    Route::delete('/two-factor/trusted-devices/{device}', [TwoFactorController::class, 'revokeTrustedDevice'])->name('two-factor.trusted-devices.revoke');
    Route::delete('/two-factor/trusted-devices', [TwoFactorController::class, 'revokeAllTrustedDevices'])->name('two-factor.trusted-devices.revoke-all');

    Route::get('/otp/challenge', [OtpController::class, 'showChallenge'])->name('otp.challenge');
    Route::post('/otp/send', [OtpController::class, 'send'])->middleware('throttle:5,1')->name('otp.send');
    Route::post('/otp/verify', [OtpController::class, 'verifyChallenge'])->middleware('throttle:10,1')->name('otp.verify');
});
