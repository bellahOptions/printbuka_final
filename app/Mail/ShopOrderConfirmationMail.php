<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\ShopOrder;
use App\Support\PdfTemplateOverrides;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderConfirmationMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public ShopOrder $order)
    {
        $this->order->loadMissing('items.selectedOptions');
    }

    public function build(): self
    {
        $pdf = Pdf::loadView('shop.receipt-pdf', [
            'order' => $this->order,
            ...PdfTemplateOverrides::forKey('pdf.shop_receipt', [
                'customer_name' => (string) $this->order->customer_name,
                'order_number' => (string) $this->order->reference,
                'total_amount' => number_format((float) $this->order->total, 2),
            ]),
        ])->output();

        return $this
            ->subject($this->templateSubject('Order confirmed! ' . $this->order->reference . ' — Printbuka'))
            ->view('mail.shop.order-confirmation')
            ->with([
                'order' => $this->order,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ])
            ->attachData($pdf, 'receipt-' . $this->order->reference . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    protected function templateKey(): string
    {
        return 'shop.order_confirmation';
    }

    protected function templateVariables(): array
    {
        return [
            'customer_name' => (string) $this->order->customer_name,
            'order_reference' => (string) $this->order->reference,
            'total_amount' => number_format((float) $this->order->total, 2),
        ];
    }
}
