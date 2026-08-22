<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobPhaseRoleAlertMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $phase
     */
    public function __construct(
        public Order $order,
        public User $recipient,
        public array $phase,
        public string $oldStatus,
        public string $newStatus
    ) {
        $this->order->loadMissing('product', 'creatorAdmin');
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('Job moved to your phase: '.$this->order->job_order_number))
            ->view('mail.jobs.phase-role-alert')
            ->with([
                'order' => $this->order,
                'recipient' => $this->recipient,
                'phase' => $this->phase,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'job.phase_role_alert';
    }

    protected function templateVariables(): array
    {
        return [
            'recipient_name' => $this->recipient->displayName(),
            'order_number' => (string) ($this->order->job_order_number ?? $this->order->displayNumber()),
            'customer_name' => (string) $this->order->customer_name,
            'phase_name' => (string) ($this->phase['phase'] ?? 'Workflow Update'),
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
