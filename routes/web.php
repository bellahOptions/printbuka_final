<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryAddressController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Local\InvoiceDesignPreviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PolicyPageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TrackOrderController;
use App\Http\Controllers\UserInvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('customer.portal')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [ProductController::class, 'byCategory'])->name('products.category');

    Route::get('/products/{product}/order', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/products/{product}/order', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');

    Route::get('/track-order', [TrackOrderController::class, 'create'])->name('orders.track');
    Route::post('/track-order', [TrackOrderController::class, 'store'])->name('orders.track.store');
    Route::get('/track-order/{order}', [TrackOrderController::class, 'show'])->name('orders.track.show');

    Route::get('/partners', [PartnerController::class, 'create'])->name('partners.create');
    Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');

    Route::get('/terms-and-conditions', [PolicyPageController::class, 'terms'])->name('policies.terms');
    Route::get('/privacy-policy', [PolicyPageController::class, 'privacy'])->name('policies.privacy');
    Route::get('/refund-policy', [PolicyPageController::class, 'refund'])->name('policies.refund');

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::post('/services/{service}/order', [ServiceOrderController::class, 'store'])->name('services.orders.store');
    Route::get('/services/{service}/orders/{order}/success', [ServiceOrderController::class, 'success'])->name('services.orders.success');

    Route::get('/get-quote', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/get-quote', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/get-quote/{order}/success', [QuoteController::class, 'success'])->name('quotes.success');

    Route::get('/payments/paystack/callback', [PaymentController::class, 'paystackCallback'])->name('payments.paystack.callback');

    Route::get('/blog', [BlogController::class, 'index'])->name('blog');
    Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
});

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

    Route::middleware('customer.portal')->group(function (): void {
        Route::middleware('user.verified')->group(function (): void {
            Route::get('/dashboard', DashboardController::class)->name('dashboard');
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/addresses', [DeliveryAddressController::class, 'store'])->name('profile.addresses.store');
            Route::put('/profile/addresses/{deliveryAddress}', [DeliveryAddressController::class, 'update'])->name('profile.addresses.update');
            Route::delete('/profile/addresses/{deliveryAddress}', [DeliveryAddressController::class, 'destroy'])->name('profile.addresses.destroy');
            Route::put('/profile/addresses/{deliveryAddress}/default', [DeliveryAddressController::class, 'setDefault'])->name('profile.addresses.default');

            Route::get('/manage-invoices', [UserInvoiceController::class, 'index'])->name('invoice.index');
        });

        Route::get('/{invoice}', [UserInvoiceController::class, 'show'])->name('show')->whereNumber('invoice');
        Route::get('/{invoice}/download', [UserInvoiceController::class, 'download'])->name('download')->whereNumber('invoice');

        Route::get('/support-tickets', [SupportController::class, 'index'])->name('support.tickets.index');
        Route::get('/support-tickets/create', [SupportController::class, 'create'])->name('support.tickets.create');
        Route::post('/support-tickets', [SupportController::class, 'store'])->name('support.tickets.store');
        Route::get('/support-tickets/{ticket}', [SupportController::class, 'show'])->name('support.tickets.show');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
});

Route::middleware(['user.auth', 'customer.portal'])->prefix('support')->name('support.')->group(function () {
    Route::get('/', [SupportController::class, 'index'])->name('index');
    Route::get('/create', [SupportController::class, 'create'])->name('create');
    Route::post('/', [SupportController::class, 'store'])->name('store');
    Route::get('/{ticket}', [SupportController::class, 'show'])->name('show');
    Route::post('/{ticket}/reply', [SupportController::class, 'reply'])->name('reply');
    Route::put('/{ticket}/close', [SupportController::class, 'close'])->name('close');
});

if (app()->environment('local')) {
    Route::prefix('/local-previews/invoices')
        ->name('local-previews.invoices.')
        ->group(function (): void {
            Route::get('/', [InvoiceDesignPreviewController::class, 'index'])->name('index');
            Route::get('/pdf', [InvoiceDesignPreviewController::class, 'invoicePdf'])->name('pdf');
            Route::get('/receipt-pdf', [InvoiceDesignPreviewController::class, 'receiptPdf'])->name('receipt-pdf');
            Route::get('/email', [InvoiceDesignPreviewController::class, 'invoiceEmail'])->name('email');
            Route::get('/paid-receipt-email', [InvoiceDesignPreviewController::class, 'paidReceiptEmail'])->name('paid-receipt-email');
        });
}
