<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('user.guest')->group(function (): void {
    Route::get('/staff/login', [AuthController::class, 'showStaffLogin'])->name('staff.login');
    Route::post('/staff/login', [AuthController::class, 'staffLogin'])->name('staff.login.store');
    Route::get('/staff/register', [AuthController::class, 'showStaffRegister'])->name('staff.register');
    Route::post('/staff/register', [AuthController::class, 'staffRegister'])->name('staff.register.store');
});
