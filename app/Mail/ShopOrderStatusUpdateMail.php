<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderStatusUpdateMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public string $statusLabel;

    public string $statusMessage;

    public string $statusColor;

    public function __construct(public ShopOrder $order, public string $newStatus)
    {
        [$this->statusLabel, $this->statusMessage, $this->statusColor] = match ($newStatus) {
            'order_received' => [
                'Order Received',
                'We have received your order and it is now in our queue. Our team will begin processing it shortly.',
                '#0369a1',
            ],
            'processing' => [
                'Order Processing',
                'Great news! Your order is now being actively prepared by our production team. We\'re working hard to get it ready.',
                '#d97706',
            ],
            'dispatched' => [
                'Order Dispatched',
                'Your order is on its way! It has been handed over to our delivery partner and is en route to your address.',
                '#7c3aed',
            ],
            'delivered' => [
                'Order Delivered',
                'Your order has been delivered! We hope you love your purchase. Thank you for choosing Printbuka.',
                '#16a34a',
            ],
            default => [
                'Order Update',
                'Your order status has been updated.',
                '#475569',
            ],
        };
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject("Order {$this->statusLabel} — {$this->order->reference} | Printbuka"))
            ->view('mail.shop.order-status-update')
            ->with([
                'order' => $this->order,
                'newStatus' => $this->newStatus,
                'statusLabel' => $this->statusLabel,
                'statusMessage' => $this->statusMessage,
                'statusColor' => $this->statusColor,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'shop.order_status_update';
    }

    protected function templateVariables(): array
    {
        return [
            'customer_name' => (string) $this->order->customer_name,
            'order_reference' => (string) $this->order->reference,
            'status_label' => $this->statusLabel,
        ];
    }
}
