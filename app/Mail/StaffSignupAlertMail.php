<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffSignupAlertMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public User $recipient, public User $staff)
    {
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('New staff signup awaiting approval'))
            ->view('mail.staff.signup-alert')
            ->with([
                'recipient' => $this->recipient,
                'staff' => $this->staff,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'staff.signup_alert';
    }

    protected function templateVariables(): array
    {
        return [
            'recipient_name' => $this->recipient->displayName(),
            'staff_name' => $this->staff->displayName(),
            'staff_email' => (string) $this->staff->email,
        ];
    }
}
