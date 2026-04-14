<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminBlogPostController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFinanceController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSiteSettingController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartnerController;
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
    Route::get('/staff/login', [AuthController::class, 'showStaffLogin'])->name('staff.login');
    Route::post('/staff/login', [AuthController::class, 'staffLogin'])->name('staff.login.store');
    Route::get('/staff/register', [AuthController::class, 'showStaffRegister'])->name('staff.register');
    Route::post('/staff/register', [AuthController::class, 'staffRegister'])->name('staff.register.store');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::middleware('user.auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->middleware(['admin.permission:admin.view', 'admin.activity'])->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::resource('products', AdminProductController::class)
            ->except('show')
            ->middleware('admin.permission:products.manage');
        Route::resource('product-categories', AdminProductCategoryController::class)
            ->except('show')
            ->middleware('admin.permission:product_categories.manage');
        Route::resource('blog', AdminBlogPostController::class)
            ->except('show')
            ->middleware('admin.permission:blog.manage');
        Route::resource('invoices', AdminInvoiceController::class)
            ->except('show')
            ->middleware('admin.permission:invoices.manage');
        Route::get('/notifications', [AdminNotificationController::class, 'index'])
            ->middleware('admin.permission:*')
            ->name('notifications.index');
        Route::post('/notifications', [AdminNotificationController::class, 'store'])
            ->middleware('admin.permission:*')
            ->name('notifications.store');
        Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])
            ->middleware('admin.permission:*')
            ->name('notifications.destroy');
        Route::resource('finance', AdminFinanceController::class)
            ->except('show')
            ->middleware('admin.permission:finance.view');
        Route::get('/settings', [AdminSiteSettingController::class, 'edit'])
            ->middleware('admin.permission:site_settings.manage')
            ->name('settings.edit');
        Route::put('/settings', [AdminSiteSettingController::class, 'update'])
            ->middleware('admin.permission:site_settings.manage')
            ->name('settings.update');
        Route::get('/staff', [AdminStaffController::class, 'index'])
            ->middleware('admin.permission:staff.view')
            ->name('staff.index');
        Route::put('/staff/{user}', [AdminStaffController::class, 'update'])
            ->middleware('admin.permission:*')
            ->name('staff.update');
        Route::get('/orders', [AdminOrderController::class, 'index'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.index');
        Route::get('/orders/create', [AdminOrderController::class, 'create'])
            ->middleware('admin.permission:orders.create')
            ->name('orders.create');
        Route::post('/orders', [AdminOrderController::class, 'store'])
            ->middleware('admin.permission:orders.create')
            ->name('orders.store');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.show');
        Route::put('/orders/{order}', [AdminOrderController::class, 'update'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.update');
    });
});
