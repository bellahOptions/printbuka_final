<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffKycReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $staff) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Action Required: Complete Your Staff Bio-Data Form — Printbuka');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff.kyc-reminder');
    }
}
