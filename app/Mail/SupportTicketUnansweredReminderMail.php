<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SupportTicketUnansweredReminderMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

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
            ->subject($this->templateSubject('Reminder: unanswered support tickets'))
            ->view('mail.support.unanswered-ticket-reminder')
            ->with([
                'recipient' => $this->recipient,
                'tickets' => $this->tickets,
                'thresholdHours' => $this->thresholdHours,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'support.unanswered_reminder';
    }

    protected function templateVariables(): array
    {
        return [
            'staff_name' => (string) $this->recipient->displayName(),
            'ticket_count' => (string) $this->tickets->count(),
            'threshold_hours' => (string) $this->thresholdHours,
        ];
    }
}
