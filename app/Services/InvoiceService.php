<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    public function createForOrder(Order $order): Invoice
    {
        $order->loadMissing('product');
        $subtotal = (float) $order->total_price;

        return Invoice::query()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'invoice_number' => $this->invoiceNumberFor($order),
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                'status' => $this->paymentStatusFor($order),
                'issued_at' => now(),
                'due_at' => now()->addDays(7),
                'sent_at' => null,
            ]
        );
    }

    public function sendInvoice(Invoice $invoice): bool
    {
        try {
            $invoice->loadMissing('order.product');
            $recipient = (string) ($invoice->order?->customer_email ?? '');

            if ($recipient === '') {
                Log::warning('Invoice email skipped because recipient is missing.', [
                    'invoice_id' => $invoice->id,
                    'order_id' => $invoice->order_id,
                ]);

                return false;
            }

            Mail::to($recipient)->send(new InvoiceMail($invoice));
            $invoice->forceFill(['sent_at' => now()])->save();

            return true;
        } catch (\Throwable $exception) {
            Log::error('Invoice email failed to send.', [
                'invoice_id' => $invoice->id,
                'order_id' => $invoice->order_id,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function invoiceNumberFor(Order $order): string
    {
        return 'PB-INV-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
    }

    private function paymentStatusFor(Order $order): string
    {
        $total = (float) $order->total_price;
        $paid = (float) $order->amount_paid;

        if ($total > 0 && $paid >= $total) {
            return 'paid';
        }

        if ($paid > 0) {
            return 'partially_paid';
        }

        return 'unpaid';
    }
}
