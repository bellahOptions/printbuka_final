<?php

use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminAdvertisementController;
use App\Http\Controllers\Admin\AdminBlogPostController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFinanceController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPolicyController;
use App\Http\Controllers\Admin\AdminProductCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminShopOrderController;
use App\Http\Controllers\Admin\AdminShopProductController;
use App\Http\Controllers\Admin\AdminSiteSettingController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminStaffProfileController;
use App\Http\Controllers\Admin\AdminStaffQueryController;
use App\Http\Controllers\Admin\AdminStaffEvaluationController;
use App\Http\Controllers\Admin\AdminPayrollController;
use App\Http\Controllers\Admin\AdminSupportTicketController;
use App\Http\Controllers\Admin\AdminTrainingApplicationController;
use App\Http\Controllers\Admin\DailyTodoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['user.auth', 'user.verified'])->group(function (): void {
    Route::prefix('admin')->name('admin.')->middleware(['admin.permission:admin.view'])->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'editAdmin'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'updateAdmin'])->name('profile.update');

        Route::delete('/products/seeded-catalog', [AdminProductController::class, 'destroySeeded'])
            ->middleware(['admin.permission:products.manage', 'super.admin'])
            ->name('products.seeded.destroy');
        Route::resource('products', AdminProductController::class)
            ->except('show')
            ->middleware('admin.permission:products.manage');
        Route::resource('product-categories', AdminProductCategoryController::class)
            ->except('show')
            ->middleware('admin.permission:product_categories.manage');
        Route::resource('blog', AdminBlogPostController::class)
            ->except('show')
            ->middleware('admin.permission:blog.manage');
        Route::post('/invoices/import-csv', [AdminInvoiceController::class, 'importCsv'])
            ->middleware(['admin.permission:invoices.manage', 'super.admin'])
            ->name('invoices.import-csv');
        Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show'])
            ->middleware('admin.permission:invoices.manage')
            ->whereNumber('invoice')
            ->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [AdminInvoiceController::class, 'download'])
            ->middleware('admin.permission:invoices.manage')
            ->whereNumber('invoice')
            ->name('invoices.download');
        Route::resource('invoices', AdminInvoiceController::class)
            ->except('show')
            ->whereNumber('invoice')
            ->middleware('admin.permission:invoices.manage');
        Route::patch('/invoices/{invoice}/mark-paid', [AdminInvoiceController::class, 'markAsPaid'])
            ->middleware('admin.permission:invoices.manage')
            ->whereNumber('invoice')
            ->name('invoices.mark-paid');
        Route::patch('/invoices/{invoice}/send', [AdminInvoiceController::class, 'send'])
            ->middleware('admin.permission:invoices.manage')
            ->whereNumber('invoice')
            ->name('invoices.send');
        Route::post('/invoices/{invoice}/record-payment', [AdminInvoiceController::class, 'recordPayment'])
            ->middleware('admin.permission:invoices.manage')
            ->whereNumber('invoice')
            ->name('invoices.record-payment');
        Route::patch('/invoices/{invoice}/payment-terms', [AdminInvoiceController::class, 'updatePaymentTerms'])
            ->middleware('admin.permission:invoices.manage')
            ->whereNumber('invoice')
            ->name('invoices.payment-terms');
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
        Route::get('/advertisements', [AdminAdvertisementController::class, 'index'])
            ->middleware('super.admin')
            ->name('advertisements.index');
        Route::post('/advertisements', [AdminAdvertisementController::class, 'store'])
            ->middleware('super.admin')
            ->name('advertisements.store');
        Route::delete('/advertisements/{advertisement}', [AdminAdvertisementController::class, 'destroy'])
            ->middleware('super.admin')
            ->name('advertisements.destroy');
        Route::get('/finance/{finance}', [AdminFinanceController::class, 'show'])
            ->middleware('admin.permission:finance.view')
            ->whereNumber('finance')
            ->name('finance.show');
        Route::get('/finance/{finance}/download', [AdminFinanceController::class, 'download'])
            ->middleware('admin.permission:finance.view')
            ->whereNumber('finance')
            ->name('finance.download');
        Route::get('/finance-reports', [AdminFinanceController::class, 'reportForm'])
            ->middleware('admin.permission:finance.view')
            ->name('finance.report-form');
        Route::get('/finance-reports/download', [AdminFinanceController::class, 'downloadReport'])
            ->middleware('admin.permission:finance.view')
            ->name('finance.report-download');
        Route::post('/finance-reports/email', [AdminFinanceController::class, 'emailReport'])
            ->middleware('admin.permission:finance.view')
            ->name('finance.report-email');
        Route::resource('finance', AdminFinanceController::class)
            ->except('show')
            ->whereNumber('finance')
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
        Route::get('/training-applications', [AdminTrainingApplicationController::class, 'index'])
            ->middleware('admin.permission:training.manage')
            ->name('training.index');
        Route::get('/training-applications/{training}', [AdminTrainingApplicationController::class, 'show'])
            ->middleware('admin.permission:training.manage')
            ->name('training.show');
        Route::patch('/training-applications/{training}/decision', [AdminTrainingApplicationController::class, 'decide'])
            ->middleware('admin.permission:training.manage')
            ->name('training.decide');
        Route::put('/staff/{user}', [AdminStaffController::class, 'update'])
            ->middleware('super.admin')
            ->name('staff.update');
        Route::patch('/staff/{user}/employment-status', [AdminStaffController::class, 'updateEmploymentStatus'])
            ->middleware('admin.permission:staff.view')
            ->name('staff.employment-status');

        // ===== STAFF PROFILE / KYC =====
        Route::get('/staff/{user}/profile', [AdminStaffProfileController::class, 'show'])
            ->name('staff.profile.show');
        Route::put('/staff/{user}/profile', [AdminStaffProfileController::class, 'update'])
            ->name('staff.profile.update');
        Route::post('/staff/{user}/kyc-complete', [AdminStaffProfileController::class, 'markKycComplete'])
            ->middleware('admin.permission:staff.kyc')
            ->name('staff.kyc-complete');

        // ===== STAFF QUERIES =====
        Route::get('/staff-queries', [AdminStaffQueryController::class, 'index'])
            ->middleware('admin.permission:staff.queries')
            ->name('staff-queries.index');
        Route::get('/staff-queries/create', [AdminStaffQueryController::class, 'create'])
            ->middleware('admin.permission:staff.queries')
            ->name('staff-queries.create');
        Route::post('/staff-queries', [AdminStaffQueryController::class, 'store'])
            ->middleware('admin.permission:staff.queries')
            ->name('staff-queries.store');
        Route::get('/staff-queries/{query}', [AdminStaffQueryController::class, 'show'])
            ->name('staff-queries.show');
        Route::post('/staff-queries/{query}/respond', [AdminStaffQueryController::class, 'respond'])
            ->name('staff-queries.respond');
        Route::post('/staff-queries/{query}/close', [AdminStaffQueryController::class, 'close'])
            ->middleware('admin.permission:staff.queries')
            ->name('staff-queries.close');

        // ===== STAFF EVALUATIONS =====
        Route::get('/evaluations', [AdminStaffEvaluationController::class, 'index'])
            ->middleware('admin.permission:staff.evaluations')
            ->name('evaluations.index');
        Route::get('/evaluations/create', [AdminStaffEvaluationController::class, 'create'])
            ->middleware('admin.permission:staff.evaluations')
            ->name('evaluations.create');
        Route::post('/evaluations', [AdminStaffEvaluationController::class, 'store'])
            ->middleware('admin.permission:staff.evaluations')
            ->name('evaluations.store');
        Route::get('/evaluations/{evaluation}', [AdminStaffEvaluationController::class, 'show'])
            ->name('evaluations.show');
        Route::put('/evaluations/{evaluation}', [AdminStaffEvaluationController::class, 'store'])
            ->middleware('admin.permission:staff.evaluations')
            ->name('evaluations.update');
        Route::post('/evaluations/{evaluation}/acknowledge', [AdminStaffEvaluationController::class, 'acknowledge'])
            ->name('evaluations.acknowledge');

        // ===== PAYROLL =====
        Route::get('/payroll', [AdminPayrollController::class, 'index'])
            ->middleware('admin.permission:payroll.view')
            ->name('payroll.index');
        Route::get('/payroll/salary-structures', [AdminPayrollController::class, 'salaryIndex'])
            ->middleware('admin.permission:payroll.view')
            ->name('payroll.salary-structures');
        Route::post('/payroll/salary-structures', [AdminPayrollController::class, 'salaryStore'])
            ->middleware('admin.permission:payroll.manage')
            ->name('payroll.salary-store');
        Route::get('/payroll/create', [AdminPayrollController::class, 'createRun'])
            ->middleware('admin.permission:payroll.manage')
            ->name('payroll.create-run');
        Route::post('/payroll', [AdminPayrollController::class, 'storeRun'])
            ->middleware('admin.permission:payroll.manage')
            ->name('payroll.store-run');
        Route::get('/payroll/{run}', [AdminPayrollController::class, 'showRun'])
            ->middleware('admin.permission:payroll.view')
            ->name('payroll.run');
        Route::patch('/payroll/entries/{entry}', [AdminPayrollController::class, 'updateEntry'])
            ->middleware('admin.permission:payroll.manage')
            ->name('payroll.update-entry');
        Route::post('/payroll/{run}/finalize', [AdminPayrollController::class, 'finalizeRun'])
            ->middleware('admin.permission:payroll.manage')
            ->name('payroll.finalize');
        Route::post('/payroll/{run}/send-payslips', [AdminPayrollController::class, 'sendPayslips'])
            ->middleware('admin.permission:payroll.manage')
            ->name('payroll.send-payslips');
        Route::get('/payroll/payslip/{entry}/download', [AdminPayrollController::class, 'downloadPayslip'])
            ->middleware('admin.permission:payroll.view')
            ->name('payroll.payslip.download');
        Route::get('/payroll/{run}/download', [AdminPayrollController::class, 'downloadRunPdf'])
            ->middleware('admin.permission:payroll.view')
            ->name('payroll.run.download');
        Route::post('/payroll/{run}/email-ceo', [AdminPayrollController::class, 'emailToCeo'])
            ->middleware('admin.permission:payroll.manage')
            ->name('payroll.run.email-ceo');

        Route::get('/customers', [AdminCustomerController::class, 'index'])
            ->middleware('admin.permission:customers.manage')
            ->name('customers.index');
        Route::patch('/customers/{customer}/status', [AdminCustomerController::class, 'updateStatus'])
            ->middleware('admin.permission:customers.manage')
            ->name('customers.update-status');
        Route::post('/customers/{customer}/message', [AdminCustomerController::class, 'sendMessage'])
            ->middleware('admin.permission:customers.manage')
            ->name('customers.send-message');
        Route::delete('/customers/{customer}', [AdminCustomerController::class, 'destroy'])
            ->middleware('super.admin')
            ->name('customers.destroy');
        Route::get('/audit-logs', [AdminActivityLogController::class, 'index'])
            ->middleware('super.admin')
            ->name('activity-logs.index');
        Route::get('/orders', [AdminOrderController::class, 'index'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.index');
        Route::get('/orders/create', [AdminOrderController::class, 'create'])
            ->middleware('admin.permission:orders.create')
            ->name('orders.create');
        Route::post('/orders/todo-reminders/send', [AdminOrderController::class, 'sendTodoReminders'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.todo-reminders.send');
        Route::post('/orders', [AdminOrderController::class, 'store'])
            ->middleware('admin.permission:orders.create')
            ->name('orders.store');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.show');
        Route::get('/orders/{order}/job-log', [AdminOrderController::class, 'jobLog'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.job-log');

        Route::get('/tasks', [DailyTodoController::class, 'index'])
            ->name('tasks.index');
        Route::post('/tasks', [DailyTodoController::class, 'store'])
            ->middleware('admin.permission:orders.view')
            ->name('tasks.store');
        Route::patch('/tasks/{todo}/mark-working', [DailyTodoController::class, 'markWorking'])
            ->name('tasks.mark-working');
        Route::patch('/tasks/{todo}/mark-done', [DailyTodoController::class, 'markDone'])
            ->name('tasks.mark-done');
        Route::patch('/tasks/{todo}/approve', [DailyTodoController::class, 'approve'])
            ->name('tasks.approve');
        Route::patch('/tasks/{todo}/reject', [DailyTodoController::class, 'reject'])
            ->name('tasks.reject');
        Route::get('/orders/{order}/job-log/download', [AdminOrderController::class, 'jobLogDownload'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.job-log.download');
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
        Route::patch('/orders/{order}/conclude', [AdminOrderController::class, 'conclude'])
            ->middleware('admin.permission:orders.view')
            ->name('orders.conclude');

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

        // ===== SHOP PRODUCTS =====
        Route::resource('shop-products', AdminShopProductController::class)
            ->except('show')
            ->middleware('admin.permission:shop-products.manage');

        // ===== SHOP ORDERS =====
        Route::get('/shop-orders', [AdminShopOrderController::class, 'index'])
            ->middleware('admin.permission:shop-orders.view')
            ->name('shop-orders.index');
        Route::get('/shop-orders/{shopOrder}', [AdminShopOrderController::class, 'show'])
            ->middleware('admin.permission:shop-orders.view')
            ->name('shop-orders.show');
        Route::patch('/shop-orders/{shopOrder}/status', [AdminShopOrderController::class, 'updateStatus'])
            ->middleware('admin.permission:shop-orders.view')
            ->name('shop-orders.update-status');
    });
});
