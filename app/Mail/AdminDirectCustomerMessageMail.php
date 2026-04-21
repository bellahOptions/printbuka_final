<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminDirectCustomerMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $recipientName,
        public string $subjectLine,
        public string $body,
    ) {}

    public function build(): self
    {
        return $this
            ->from($this->senderEmail, $this->senderName)
            ->replyTo($this->senderEmail, $this->senderName)
            ->subject($this->subjectLine)
            ->view('mail.customers.admin-direct-message')
            ->with([
                'senderName' => $this->senderName,
                'recipientName' => $this->recipientName,
                'messageBody' => $this->body,
            ]);
    }
}
