<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Invoice;
use App\Support\PdfTemplateOverrides;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->invoice->loadMissing('order.product');
    }

    public function build(): self
    {
        $documentType = $this->invoice->documentTypeLabel();
        $documentTypeSlug = str($documentType)->lower()->replace(' ', '-')->value();

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
            ...PdfTemplateOverrides::forKey('pdf.invoice_customer', $this->templateVariables()),
        ])->output();

        return $this
            ->subject($this->templateSubject('Your Printbuka '.strtolower($documentType).' '.$this->invoice->invoice_number))
            ->view('mail.invoices.created')
            ->with([
                'invoice' => $this->invoice,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ])
            ->attachData($pdf, $documentTypeSlug.'-'.$this->invoice->invoice_number.'.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    protected function templateKey(): string
    {
        return 'invoice.created';
    }

    protected function templateVariables(): array
    {
        return [
            'customer_name' => (string) $this->invoice->order->customer_name,
            'invoice_number' => (string) $this->invoice->invoice_number,
            'total_amount' => number_format((float) $this->invoice->total_amount, 2),
            'order_number' => (string) ($this->invoice->order->job_order_number ?? $this->invoice->order->displayNumber()),
        ];
    }
}
