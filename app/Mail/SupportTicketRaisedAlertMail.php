<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportTicketRaisedAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $recipient, public Ticket $ticket)
    {
        $this->ticket->loadMissing('user');
    }

    public function build(): self
    {
        return $this
            ->subject('New support ticket raised: '.$this->ticket->ticket_number)
            ->view('mail.support.ticket-raised')
            ->with([
                'recipient' => $this->recipient,
                'ticket' => $this->ticket,
            ]);
    }
}
