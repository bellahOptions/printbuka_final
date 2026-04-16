<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobAssignedDesignerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public User $designer)
    {
        $this->order->loadMissing('product', 'creatorAdmin');
    }

    public function build(): self
    {
        return $this
            ->subject('New job assigned: '.$this->order->job_order_number)
            ->view('mail.jobs.assigned-designer')
            ->with([
                'order' => $this->order,
                'designer' => $this->designer,
            ]);
    }
}
