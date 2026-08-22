<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportTicketRaisedAlertMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public User $recipient, public Ticket $ticket)
    {
        $this->ticket->loadMissing('user');
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('New support ticket raised: '.$this->ticket->ticket_number))
            ->view('mail.support.ticket-raised')
            ->with([
                'recipient' => $this->recipient,
                'ticket' => $this->ticket,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'support.ticket_raised';
    }

    protected function templateVariables(): array
    {
        return [
            'staff_name' => (string) $this->recipient->displayName(),
            'ticket_number' => (string) $this->ticket->ticket_number,
            'ticket_subject' => (string) $this->ticket->subject,
        ];
    }
}
