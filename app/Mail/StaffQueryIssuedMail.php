<?php

namespace App\Mail;

use App\Models\StaffQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffQueryIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly StaffQuery $query) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Staff Query '.$this->query->query_number.': '.$this->query->subject.' — Printbuka HR');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff.query-issued');
    }
}
