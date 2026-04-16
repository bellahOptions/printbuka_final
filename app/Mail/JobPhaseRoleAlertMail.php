<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobPhaseRoleAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $phase
     */
    public function __construct(
        public Order $order,
        public User $recipient,
        public array $phase,
        public string $oldStatus,
        public string $newStatus
    ) {
        $this->order->loadMissing('product', 'creatorAdmin');
    }

    public function build(): self
    {
        return $this
            ->subject('Job moved to your phase: '.$this->order->job_order_number)
            ->view('mail.jobs.phase-role-alert')
            ->with([
                'order' => $this->order,
                'recipient' => $this->recipient,
                'phase' => $this->phase,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
            ]);
    }
}
