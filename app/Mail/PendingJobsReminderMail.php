<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendingJobsReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{order:\App\Models\Order,phase:string,status:string,stuck_hours:int}>  $items
     */
    public function __construct(public User $recipient, public array $items)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Pending jobs reminder - '.count($this->items).' job(s)')
            ->view('mail.jobs.pending-jobs-reminder')
            ->with([
                'recipient' => $this->recipient,
                'items' => $this->items,
            ]);
    }
}
