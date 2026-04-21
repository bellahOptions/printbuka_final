<?php

use App\Http\Controllers\Admin\AdminBlogPostController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFinanceController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminNewsletterController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPolicyController;
use App\Http\Controllers\Admin\AdminProductCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSiteSettingController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminSupportTicketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route; 

Route::middleware(['user.auth', 'user.verified'])->group(function (): void {
    Route::prefix('admin')->name('admin.')->middleware(['admin.permission:admin.view', 'admin.activity'])->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'editAdmin'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'updateAdmin'])->name('profile.update');

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
        Route::patch('/invoices/{invoice}/mark-paid', [AdminInvoiceController::class, 'markAsPaid'])
            ->middleware('admin.permission:invoices.manage')
            ->name('invoices.mark-paid');
        Route::get('/invoices/quotations/create', [AdminInvoiceController::class, 'createQuotation'])
            ->middleware('admin.permission:invoices.manage')
            ->name('invoices.quotations.create');
        Route::post('/invoices/quotations', [AdminInvoiceController::class, 'storeQuotation'])
            ->middleware('admin.permission:invoices.manage')
            ->name('invoices.quotations.store');
        Route::get('/notifications', [AdminNotificationController::class, 'index'])
            ->middleware('admin.permission:*')
            ->name('notifications.index');
        Route::post('/notifications', [AdminNotificationController::class, 'store'])
            ->middleware('admin.permission:*')
            ->name('notifications.store');
        Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])
            ->middleware('admin.permission:*')
            ->name('notifications.destroy');
        Route::get('/newsletters', [AdminNewsletterController::class, 'index'])
            ->middleware('admin.permission:newsletters.manage')
            ->name('newsletters.index');
        Route::post('/newsletters', [AdminNewsletterController::class, 'store'])
            ->middleware('admin.permission:newsletters.manage')
            ->name('newsletters.store');
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
            ->middleware('super.admin')
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
        Route::post('/orders/{order}/receive-brief', [AdminOrderController::class, 'receiveBrief'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.receive-brief');
        Route::post('/orders/{order}/move-forward', [AdminOrderController::class, 'moveForward'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.move-forward');
        Route::post('/orders/{order}/approve-forward', [AdminOrderController::class, 'approveForward'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.approve-forward');

        Route::get('/support', [AdminSupportTicketController::class, 'index'])
            ->middleware('admin.permission:admin.view')
            ->name('support.index');
        Route::get('/support/create', [AdminSupportTicketController::class, 'create'])
            ->middleware('admin.permission:admin.view')
            ->name('support.create');
        Route::post('/support', [AdminSupportTicketController::class, 'store'])
            ->middleware('admin.permission:admin.view')
            ->name('support.store');
        Route::get('/support/{ticket}', [AdminSupportTicketController::class, 'show'])
            ->middleware('admin.permission:admin.view')
            ->name('support.show');
        Route::post('/support/{ticket}/reply', [AdminSupportTicketController::class, 'reply'])
            ->middleware('admin.permission:admin.view')
            ->name('support.reply');
        Route::put('/support/{ticket}/close', [AdminSupportTicketController::class, 'close'])
            ->middleware('admin.permission:admin.view')
            ->name('support.close');

        Route::get('/policies', [AdminPolicyController::class, 'edit'])
            ->middleware('super.admin')
            ->name('policies.edit');
        Route::put('/policies/terms', [AdminPolicyController::class, 'updateTerms'])
            ->middleware('super.admin')
            ->name('policies.terms.update');
        Route::put('/policies/privacy', [AdminPolicyController::class, 'updatePrivacy'])
            ->middleware('super.admin')
            ->name('policies.privacy.update');
        Route::put('/policies/refund', [AdminPolicyController::class, 'updateRefund'])
            ->middleware('super.admin')
            ->name('policies.refund.update');
    });
});
