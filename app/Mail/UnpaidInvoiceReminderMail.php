<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnpaidInvoiceReminderMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->invoice->loadMissing('order.product');
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('Reminder: Printbuka '.$this->invoice->documentTypeLabel().' '.$this->invoice->invoice_number.' is unpaid'))
            ->view('mail.invoices.unpaid-reminder')
            ->with([
                'invoice' => $this->invoice,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'invoice.unpaid_reminder';
    }

    protected function templateVariables(): array
    {
        return [
            'customer_name' => (string) ($this->invoice->order?->customer_name ?? ''),
            'invoice_number' => (string) $this->invoice->invoice_number,
            'total_amount' => number_format((float) $this->invoice->total_amount, 2),
        ];
    }
}
