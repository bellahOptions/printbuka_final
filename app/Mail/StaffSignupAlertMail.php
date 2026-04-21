<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffSignupAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $recipient, public User $staff)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('New staff signup awaiting approval')
            ->view('mail.staff.signup-alert')
            ->with([
                'recipient' => $this->recipient,
                'staff' => $this->staff,
            ]);
    }
}
