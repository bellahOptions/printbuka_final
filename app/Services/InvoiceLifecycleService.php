<?php

namespace App\Services;

use App\Mail\InvoicePaidReceiptMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceLifecycleService
{
    public function handleStatusChange(Invoice $invoice, ?string $previousStatus = null): void
    {
        $invoice->loadMissing('order.product');

        $oldStatus = $previousStatus ?? $invoice->getOriginal('status');
        $newStatus = (string) $invoice->status;

        if ($newStatus !== 'paid' || $oldStatus === 'paid') {
            return;
        }

        if (! $invoice->paid_at) {
            $invoice->forceFill(['paid_at' => now()])->save();
        }

        $order = $invoice->order;

        if ($order) {
            $total = (float) $invoice->total_amount;
            $currentPaid = (float) $order->amount_paid;

            $order->forceFill([
                'amount_paid' => max($currentPaid, $total),
                'payment_status' => 'Invoice Settled (100%)',
            ])->save();
        }

        $recipient = (string) ($order?->customer_email ?? '');

        if ($recipient === '') {
            return;
        }

        try {
            Mail::to($recipient)->send(new InvoicePaidReceiptMail($invoice));
        } catch (\Throwable $exception) {
            Log::error('Invoice paid receipt email failed.', [
                'invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
