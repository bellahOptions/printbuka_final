<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffEmploymentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

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
            ->subject('Printbuka '.$label)
            ->view('mail.staff.employment-status')
            ->with([
                'staff' => $this->staff,
                'status' => $this->status,
                'statusLabel' => $label,
                'reason' => $this->reason,
            ]);
    }
}
