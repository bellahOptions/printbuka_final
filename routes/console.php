<?php

use App\Services\AttendanceProcessingService;
use App\Services\PendingJobReminderService;
use App\Services\StaffActivitySummaryService;
use App\Services\StaffRatingService;
use App\Services\SupportTicketNotificationService;
use App\Services\UnpaidInvoiceReminderService;
use App\Models\Order;
use App\Models\Training;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('jobs:send-pending-reminders', function () {
    $sent = app(PendingJobReminderService::class)->sendReminders();
    $this->info('Pending job reminders sent: '.$sent);
})->purpose('Send reminder emails for jobs stuck in workflow phases');

Artisan::command('staff:send-daily-activity-summary', function () {
    $sent = app(StaffActivitySummaryService::class)->sendDailySummary();
    $this->info('HR daily activity summaries sent: '.$sent);
})->purpose('Send end-of-business-day staff activity summaries to HR');

Artisan::command('support:send-unanswered-ticket-reminders', function () {
    $sent = app(SupportTicketNotificationService::class)->sendUnansweredReminders();
    $this->info('Unanswered support ticket reminders sent: '.$sent);
})->purpose('Send reminder emails for unanswered support tickets');

Artisan::command('invoices:send-unpaid-reminders', function () {
    $sent = app(UnpaidInvoiceReminderService::class)->sendReminders();
    $this->info('Unpaid invoice reminders sent: '.$sent);
})->purpose('Send reminder emails for unpaid invoices every 24 hours');

Artisan::command('trainings:prune-duplicate-applications', function () {
    $deleted = 0;

    Training::query()
        ->whereNotNull('email')
        ->orderBy('id')
        ->get()
        ->groupBy(fn (Training $training): string => str((string) $training->email)->lower()->trim()->toString())
        ->each(function ($applications) use (&$deleted): void {
            $duplicateIds = $applications->slice(1)->pluck('id');

            if ($duplicateIds->isEmpty()) {
                return;
            }

            $deleted += Training::query()->whereKey($duplicateIds)->delete();
        });

    $this->info('Duplicate training applications deleted: '.$deleted);
})->purpose('Delete duplicate training applications by email, keeping the first submission');

Artisan::command('orders:prune-uninvoiced', function () {
    // Soft delete only — recoverable via Order::withTrashed(). Cancelled/On
    // Hold jobs are left alone deliberately: those are consciously-ended
    // states that deserve manual review, not an automatic sweep.
    $cutoff = now()->subDays(7);

    $orders = Order::query()
        ->whereDoesntHave('invoice')
        ->where('created_at', '<=', $cutoff)
        ->whereNotIn('status', ['Cancelled', 'On Hold'])
        ->get();

    foreach ($orders as $order) {
        Log::info('Auto-pruning uninvoiced job order (soft delete).', [
            'job_order_number' => $order->job_order_number,
            'created_at' => $order->created_at?->toDateTimeString(),
            'status' => $order->status,
        ]);
    }

    Order::query()->whereKey($orders->pluck('id'))->delete();

    $this->info('Uninvoiced jobs soft-deleted (created over 7 days ago, no invoice, not Cancelled/On Hold): '.$orders->count());
})->purpose('Soft-delete jobs older than 7 days that never received an invoice');

Artisan::command('attendance:process-daily', function () {
    $result = app(AttendanceProcessingService::class)->processDaily();
    $this->info('Attendance processed — marked absent: '.$result['absent'].', auto-closed: '.$result['closed'].'.');
})->purpose('Mark no-shows as absent past the shift cutoff and auto-close forgotten clock-outs');

Artisan::command('staff-ratings:snapshot', function () {
    $service = app(StaffRatingService::class);
    $week = $service->snapshotCurrentWeek();
    $month = $service->snapshotCurrentMonth();
    $this->info('Staff rating snapshots recomputed — '.$week->count().' staff (week), '.$month->count().' staff (month).');
})->purpose('Recompute this week\'s and this month\'s staff rating leaderboards');

Schedule::command('jobs:send-pending-reminders')->everySixHours();
Schedule::command('support:send-unanswered-ticket-reminders')->everySixHours();
Schedule::command('invoices:send-unpaid-reminders')->hourly();
Schedule::command('trainings:prune-duplicate-applications')->hourly();
Schedule::command('attendance:process-daily')
    ->hourly()
    ->timezone(config('app.business_timezone', 'Africa/Lagos'));
Schedule::command('staff:send-daily-activity-summary')
    ->weekdays()
    ->timezone(config('app.business_timezone', 'Africa/Lagos'))
    ->at('20:00');
Schedule::command('staff-ratings:snapshot')
    ->dailyAt('21:00')
    ->timezone(config('app.business_timezone', 'Africa/Lagos'));
Schedule::command('orders:prune-uninvoiced')
    ->dailyAt('03:00')
    ->timezone(config('app.business_timezone', 'Africa/Lagos'));
