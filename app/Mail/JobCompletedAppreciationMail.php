<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobCompletedAppreciationMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('product', 'invoice');
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('Thank you for choosing Printbuka · '.$this->order->job_order_number))
            ->view('mail.jobs.completed-appreciation', [
                'order' => $this->order,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'job.completed_appreciation';
    }

    protected function templateVariables(): array
    {
        return [
            'customer_name' => (string) $this->order->customer_name,
            'order_number' => (string) $this->order->job_order_number,
        ];
    }
}

