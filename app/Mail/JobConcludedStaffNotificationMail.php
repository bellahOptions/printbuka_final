<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobConcludedStaffNotificationMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public User $recipient, public Order $order)
    {
        $this->order->loadMissing('product', 'concludedBy');
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('Job Concluded · '.$this->order->job_order_number))
            ->view('mail.jobs.concluded-staff-notice', [
                'recipient' => $this->recipient,
                'order' => $this->order,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'job.concluded_staff_notice';
    }

    protected function templateVariables(): array
    {
        return [
            'recipient_name' => $this->recipient->displayName(),
            'order_number' => (string) $this->order->job_order_number,
            'customer_name' => (string) $this->order->customer_name,
            'concluded_by' => (string) ($this->order->concludedBy?->displayName() ?? 'N/A'),
        ];
    }
}
