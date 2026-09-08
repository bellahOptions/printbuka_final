<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpSystemToggledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public User $actor,
        public bool $enabled,
        public bool $isActingAdmin,
    ) {
    }

    public function build(): self
    {
        $state = $this->enabled ? 'turned on' : 'turned off';

        $subject = $this->isActingAdmin
            ? "You {$state} email OTP verification for staff logins"
            : "Email OTP verification was {$state} for staff logins";

        return $this
            ->subject($subject)
            ->view('mail.admin.otp-toggle-notice', [
                'recipient' => $this->recipient,
                'actor' => $this->actor,
                'enabled' => $this->enabled,
                'isActingAdmin' => $this->isActingAdmin,
            ]);
    }
}
