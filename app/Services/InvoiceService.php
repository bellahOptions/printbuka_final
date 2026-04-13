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
                'status' => 'sent',
                'issued_at' => now(),
                'due_at' => now()->addDays(7),
                'sent_at' => now(),
            ]
        );
    }

    public function sendInvoice(Invoice $invoice): bool
    {
        try {
            Mail::to($invoice->order->customer_email)->send(new InvoiceMail($invoice));

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
}
