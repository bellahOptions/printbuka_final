<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffEmploymentStatusMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(
        public User $staff,
        public string $status,
        public ?string $reason = null
    ) {}

    public function build(): self
    {
        $label = match ($this->status) {
            'suspended' => 'Suspension notice',
            'terminated' => 'Termination of contract',
            default => 'Onboarding notice',
        };

        return $this
            ->subject($this->templateSubject('Printbuka '.$label))
            ->view('mail.staff.employment-status')
            ->with([
                'staff' => $this->staff,
                'status' => $this->status,
                'statusLabel' => $label,
                'reason' => $this->reason,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'staff.employment_status';
    }

    protected function templateVariables(): array
    {
        return [
            'staff_name' => $this->staff->displayName(),
            'status' => $this->status,
            'reason' => (string) ($this->reason ?? ''),
        ];
    }
}
