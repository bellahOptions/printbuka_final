<?php

namespace App\Services;

use App\Mail\InvoicePaidReceiptMail;
use App\Models\FinanceEntry;
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
                'estimated_delivery_at' => $order->is_express
                    ? app(OrderFulfillmentService::class)->estimateExpressDelivery($invoice->paid_at ?? now())
                    : $order->estimated_delivery_at,
            ])->save();

            $this->syncFinanceIncomeEntry($invoice);
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

    private function syncFinanceIncomeEntry(Invoice $invoice): void
    {
        $order = $invoice->order;

        if (! $order) {
            return;
        }

        $description = trim($invoice->documentTypeLabel().' '.$invoice->invoice_number.' paid');

        FinanceEntry::query()->updateOrCreate(
            [
                'order_id' => $order->id,
                'type' => 'income',
                'category' => 'Invoice Payment',
                'description' => $description,
            ],
            [
                'entry_date' => ($invoice->paid_at ?? now())->toDateString(),
                'payee' => $order->customer_name,
                'amount' => (float) $invoice->total_amount,
                'payment_method' => $invoice->payment_gateway ?: 'Online',
                'notes' => trim('Auto-generated from paid '.$invoice->documentTypeLabel().'. Reference: '.($invoice->payment_reference ?: 'N/A')),
            ]
        );
    }
}
