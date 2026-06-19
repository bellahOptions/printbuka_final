<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ShopOrder $order)
    {
        $this->order->loadMissing('items.selectedOptions');
    }

    public function build(): self
    {
        $pdf = Pdf::loadView('shop.receipt-pdf', ['order' => $this->order])->output();

        return $this
            ->subject('Order confirmed! ' . $this->order->reference . ' — Printbuka')
            ->view('mail.shop.order-confirmation')
            ->with(['order' => $this->order])
            ->attachData($pdf, 'receipt-' . $this->order->reference . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
