<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobStatusAdvancedCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $oldStatus, public string $newStatus)
    {
        $this->order->loadMissing('product');
    }

    public function build(): self
    {
        return $this
            ->subject('Order update: '.$this->order->job_order_number)
            ->view('mail.jobs.status-advanced-customer')
            ->with([
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
            ]);
    }
}
