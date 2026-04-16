<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Order;
use App\Support\ReferenceCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    public function createForOrder(Order $order): Invoice
    {
        $order->loadMissing('product');
        $subtotal = (float) $order->total_price;

        $invoice = Invoice::query()->firstOrNew(['order_id' => $order->id]);
        $invoice->fill([
            'invoice_number' => $invoice->invoice_number ?: $this->invoiceNumberFor(),
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $subtotal,
            'status' => $this->paymentStatusFor($order),
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
            'sent_at' => null,
        ]);
        $invoice->save();

        return $invoice;
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

    private function invoiceNumberFor(): string
    {
        return ReferenceCode::invoiceNumber();
    }

    private function paymentStatusFor(Order $order): string
    {
        $total = (float) $order->total_price;
        $paid = (float) $order->amount_paid;

        if ($total > 0 && $paid >= $total) {
            return 'paid';
        }

        return 'unpaid';
    }
}
