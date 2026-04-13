<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->invoice->loadMissing('order.product');
    }

    public function build(): self
    {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
        ])->output();

        return $this
            ->subject('Your Printbuka invoice '.$this->invoice->invoice_number)
            ->view('mail.invoices.created')
            ->with([
                'invoice' => $this->invoice,
            ])
            ->attachData($pdf, 'invoice-'.$this->invoice->invoice_number.'.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
