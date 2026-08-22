<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffKycReminderMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public readonly User $staff) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->templateSubject('Action Required: Complete Your Staff Bio-Data Form — Printbuka'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff.kyc-reminder', with: [
            'introHtml' => $this->templateIntroHtml(),
            'outroHtml' => $this->templateOutroHtml(),
        ]);
    }

    protected function templateKey(): string
    {
        return 'staff.kyc_reminder';
    }

    protected function templateVariables(): array
    {
        return [
            'staff_name' => $this->staff->displayName(),
            'company_name' => (string) (\App\Support\SiteSettings::all()['site_name'] ?? config('app.name', 'Printbuka')),
        ];
    }
}
