<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\StaffQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffQueryIssuedMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public readonly StaffQuery $query) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->templateSubject('Staff Query '.$this->query->query_number.': '.$this->query->subject.' — Printbuka HR'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff.query-issued', with: [
            'introHtml' => $this->templateIntroHtml(),
            'outroHtml' => $this->templateOutroHtml(),
        ]);
    }

    protected function templateKey(): string
    {
        return 'staff.query_issued';
    }

    protected function templateVariables(): array
    {
        return [
            'staff_name' => (string) $this->query->staff?->displayName(),
            'query_number' => (string) $this->query->query_number,
            'query_subject' => (string) $this->query->subject,
            'query_type' => (string) $this->query->typeLabel(),
        ];
    }
}
