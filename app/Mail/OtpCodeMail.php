<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $code, public int $expiryMinutes)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Your Printbuka sign-in code: '.$this->code)
            ->view('mail.auth.otp-code', [
                'user' => $this->user,
                'code' => $this->code,
                'expiryMinutes' => $this->expiryMinutes,
            ]);
    }
}
