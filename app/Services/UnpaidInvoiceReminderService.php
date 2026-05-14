<?php

namespace App\Services;

use App\Mail\UnpaidInvoiceReminderMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UnpaidInvoiceReminderService
{
    public function sendReminders(): int
    {
        $sent = 0;

        Invoice::query()
            ->with('order.product')
            ->where('status', 'unpaid')
            ->whereNotNull('sent_at')
            ->where(function ($query): void {
                $query->whereNull('last_unpaid_reminder_sent_at')
                    ->orWhere('last_unpaid_reminder_sent_at', '<=', now()->subDay());
            })
            ->orderBy('id')
            ->chunkById(100, function ($invoices) use (&$sent): void {
                foreach ($invoices as $invoice) {
                    $recipient = (string) ($invoice->order?->customer_email ?? '');

                    if ($recipient === '') {
                        continue;
                    }

                    try {
                        Mail::to($recipient)->send(new UnpaidInvoiceReminderMail($invoice));
                        $invoice->forceFill(['last_unpaid_reminder_sent_at' => now()])->save();
                        $sent++;
                    } catch (\Throwable $exception) {
                        Log::error('Unpaid invoice reminder failed.', [
                            'invoice_id' => $invoice->id,
                            'order_id' => $invoice->order_id,
                            'message' => $exception->getMessage(),
                        ]);
                    }
                }
            });

        return $sent;
    }
}
