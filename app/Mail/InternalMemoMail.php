<?php

namespace App\Mail;

use App\Models\InternalMemo;
use App\Models\User;
use App\Support\EmailBlockRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InternalMemoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public InternalMemo $memo, public User $recipient) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->memo->subject);
    }

    public function content(): Content
    {
        $bodyHtml = EmailBlockRenderer::render($this->memo->blocks ?? [], [
            'staff_name' => $this->recipient->displayName(),
            'company_name' => (string) (\App\Support\SiteSettings::all()['site_name'] ?? config('app.name', 'Printbuka')),
        ]);

        return new Content(view: 'mail.staff.internal-memo', with: [
            'memo' => $this->memo,
            'recipient' => $this->recipient,
            'bodyHtml' => $bodyHtml,
        ]);
    }
}
