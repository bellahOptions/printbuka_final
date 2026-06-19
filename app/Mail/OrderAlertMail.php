<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderAlertMail extends Mailable
{
    use Queueable, SerializesModels;

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
            ->subject($this->subject)
            ->view('mail.admin.order-alert')
            ->with([
                'recipient'  => $this->recipient,
                'order'      => $this->order,
                'alertType'  => $this->alertType,
            ]);
    }
}
