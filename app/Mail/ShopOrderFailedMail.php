<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderFailedMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public ShopOrder $order) {}

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('Payment unsuccessful — Printbuka Order ' . $this->order->reference))
            ->view('mail.shop.order-failed')
            ->with([
                'order' => $this->order,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'shop.order_failed';
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
