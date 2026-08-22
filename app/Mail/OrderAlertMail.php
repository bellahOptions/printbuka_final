<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Order;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderAlertMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public string $alertType;   // 'shop_order' | 'quote_request'
    public string $subject;

    public function __construct(
        public User $recipient,
        public ShopOrder|Order $order,
    ) {
        $this->alertType = $order instanceof ShopOrder ? 'shop_order' : 'quote_request';

        $this->subject = $this->alertType === 'shop_order'
            ? '🛍️ New Shop Order: ' . $order->reference . ' — Printbuka'
            : '📋 New Quote Request: ' . $order->job_order_number . ' — Printbuka';
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject($this->subject))
            ->view('mail.admin.order-alert')
            ->with([
                'recipient'  => $this->recipient,
                'order'      => $this->order,
                'alertType'  => $this->alertType,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'order.alert';
    }

    protected function templateVariables(): array
    {
        return [
            'recipient_name' => $this->recipient->displayName(),
            'customer_name' => (string) $this->order->customer_name,
            'order_number' => $this->alertType === 'shop_order'
                ? (string) $this->order->reference
                : (string) $this->order->job_order_number,
            'alert_type' => $this->alertType,
        ];
    }
}
