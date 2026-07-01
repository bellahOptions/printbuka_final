<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffKycReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $staff,
        public readonly string $status,
        public readonly ?string $notes,
        public readonly string $reviewerName,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved'
            ? 'Your KYC Has Been Approved — Printbuka'
            : 'Action Required: KYC Correction Requested — Printbuka';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff.kyc-review');
    }
}
