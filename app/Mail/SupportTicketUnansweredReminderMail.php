<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SupportTicketUnansweredReminderMail extends Mailable
{
    /**
     * @param  Collection<int, Ticket>  $tickets
     */
    public function __construct(
        public User $recipient,
        public Collection $tickets,
        public int $thresholdHours
    ) {
        $this->tickets->loadMissing('user');
    }

    public function build(): self
    {
        return $this
            ->subject('Reminder: unanswered support tickets')
            ->view('mail.support.unanswered-ticket-reminder')
            ->with([
                'recipient' => $this->recipient,
                'tickets' => $this->tickets,
                'thresholdHours' => $this->thresholdHours,
            ]);
    }
}
