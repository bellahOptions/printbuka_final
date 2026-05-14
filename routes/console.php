<?php

use App\Services\PendingJobReminderService;
use App\Services\StaffActivitySummaryService;
use App\Services\SupportTicketNotificationService;
use App\Services\UnpaidInvoiceReminderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
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

Schedule::command('jobs:send-pending-reminders')->everySixHours();
Schedule::command('support:send-unanswered-ticket-reminders')->everySixHours();
Schedule::command('invoices:send-unpaid-reminders')->hourly();
Schedule::command('staff:send-daily-activity-summary')
    ->weekdays()
    ->timezone(config('app.business_timezone', 'Africa/Lagos'))
    ->at('20:00');
