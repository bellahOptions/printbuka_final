<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ShopOrder $order) {}

    public function build(): self
    {
        return $this
            ->subject('Payment unsuccessful — Printbuka Order ' . $this->order->reference)
            ->view('mail.shop.order-failed')
            ->with(['order' => $this->order]);
    }
}
