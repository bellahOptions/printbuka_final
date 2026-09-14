<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\StaffQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffQueryClosedMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public readonly StaffQuery $query) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->templateSubject('Query '.$this->query->query_number.' Closed — Conversation Log — Printbuka HR'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff.query-closed', with: [
            'introHtml' => $this->templateIntroHtml(),
            'outroHtml' => $this->templateOutroHtml(),
            'thread'    => $this->query->conversationThread(),
        ]);
    }

    protected function templateKey(): string
    {
        return 'staff.query_closed';
    }

    protected function templateVariables(): array
    {
        return [
            'staff_name'    => (string) $this->query->staff?->displayName(),
            'query_number'  => (string) $this->query->query_number,
            'query_subject' => (string) $this->query->subject,
            'query_type'    => (string) $this->query->typeLabel(),
        ];
    }
}
