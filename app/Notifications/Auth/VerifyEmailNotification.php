<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail(mixed $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your email address — Printbuka')
            ->view('mail.auth.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
            ]);
    }

    /**
     * The `verification.verify` route is protected by `signed:relative`
     * (so the link keeps working even if the request host differs from the
     * one used to generate it, e.g. behind a proxy). That middleware checks
     * the signature against the relative path only, so the signature must
     * be generated the same way (absolute: false) — otherwise every link
     * fails signature validation and no one can ever verify their email.
     */
    protected function verificationUrl($notifiable): string
    {
        $relative = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes((int) config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            absolute: false
        );

        return url($relative);
    }
}
