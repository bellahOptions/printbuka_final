<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryAddressController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TrackOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/order', [OrderController::class, 'create'])->name('orders.create');
Route::post('/products/{product}/order', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');
Route::get('/track-order', [TrackOrderController::class, 'create'])->name('orders.track');
Route::post('/track-order', [TrackOrderController::class, 'store'])->name('orders.track.store');
Route::get('/track-order/{order}', [TrackOrderController::class, 'show'])->name('orders.track.show');
Route::get('/partners', [PartnerController::class, 'create'])->name('partners.create');
Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
Route::get('/get-quote', [QuoteController::class, 'create'])->name('quotes.create');
Route::post('/get-quote', [QuoteController::class, 'store'])->name('quotes.store');
Route::get('/get-quote/{order}/success', [QuoteController::class, 'success'])->name('quotes.success');

Route::middleware('user.guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed:relative', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:6,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('user.auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('user.verified')->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/addresses', [DeliveryAddressController::class, 'store'])->name('profile.addresses.store');
        Route::put('/profile/addresses/{deliveryAddress}', [DeliveryAddressController::class, 'update'])->name('profile.addresses.update');
        Route::delete('/profile/addresses/{deliveryAddress}', [DeliveryAddressController::class, 'destroy'])->name('profile.addresses.destroy');
        Route::put('/profile/addresses/{deliveryAddress}/default', [DeliveryAddressController::class, 'setDefault'])->name('profile.addresses.default');
    });
});
