<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail(mixed $notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);
        $expiryMinutes = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Reset your password — Printbuka')
            ->view('mail.auth.reset-password', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'expiryMinutes' => $expiryMinutes,
            ]);
    }
}
