<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobStatusAdvancedCustomerMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public Order $order, public string $oldStatus, public string $newStatus)
    {
        $this->order->loadMissing('product');
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('Order update: '.$this->order->job_order_number))
            ->view('mail.jobs.status-advanced-customer')
            ->with([
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'job.status_advanced_customer';
    }

    protected function templateVariables(): array
    {
        return [
            'customer_name' => (string) $this->order->customer_name,
            'order_number' => (string) ($this->order->job_order_number ?? $this->order->displayNumber()),
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
