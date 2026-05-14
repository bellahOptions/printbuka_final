<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnpaidInvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->invoice->loadMissing('order.product');
    }

    public function build(): self
    {
        return $this
            ->subject('Reminder: Printbuka '.$this->invoice->documentTypeLabel().' '.$this->invoice->invoice_number.' is unpaid')
            ->view('mail.invoices.unpaid-reminder')
            ->with(['invoice' => $this->invoice]);
    }
}
