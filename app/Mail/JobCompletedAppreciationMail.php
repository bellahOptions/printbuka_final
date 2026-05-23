<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobCompletedAppreciationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('product', 'invoice');
    }

    public function build(): self
    {
        return $this
            ->subject('Thank you for choosing Printbuka · '.$this->order->job_order_number)
            ->view('mail.jobs.completed-appreciation', [
                'order' => $this->order,
            ]);
    }
}

