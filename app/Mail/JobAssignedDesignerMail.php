<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobAssignedDesignerMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public Order $order, public User $designer)
    {
        $this->order->loadMissing('product', 'creatorAdmin');
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('New job assigned: '.$this->order->job_order_number))
            ->view('mail.jobs.assigned-designer')
            ->with([
                'order' => $this->order,
                'designer' => $this->designer,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'job.assigned_designer';
    }

    protected function templateVariables(): array
    {
        return [
            'designer_name' => $this->designer->displayName(),
            'order_number' => (string) ($this->order->job_order_number ?? $this->order->displayNumber()),
            'customer_name' => (string) $this->order->customer_name,
            'product_name' => (string) ($this->order->product?->name ?? ($this->order->job_type ?? 'Custom order')),
        ];
    }
}
