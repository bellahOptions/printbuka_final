<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobConclusionSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $recipient, public Order $order)
    {
        $this->order->loadMissing([
            'product',
            'invoice',
            'briefReceiver',
            'creatorAdmin',
            'designer',
            'productionOfficer',
            'qcOfficer',
            'dispatcher',
            'verifier',
            'concludedBy',
        ]);
    }

    public function build(): self
    {
        $expenseEntries = $this->order->financeEntries()
            ->where('type', 'expense')
            ->with('recorder')
            ->orderByDesc('entry_date')
            ->get();

        $jobLogPdf = Pdf::loadView('admin.orders.job-log-pdf', [
            'order' => $this->order,
            'expenseEntries' => $expenseEntries,
            'asPdf' => true,
        ])->output();

        $expenseLogPdf = Pdf::loadView('admin.orders.expense-log-pdf', [
            'order' => $this->order,
            'expenseEntries' => $expenseEntries,
        ])->output();

        return $this
            ->subject('Job Concluded · '.$this->order->job_order_number)
            ->view('mail.jobs.concluded-summary', [
                'recipient' => $this->recipient,
                'order' => $this->order,
                'expenseEntries' => $expenseEntries,
            ])
            ->attachData($jobLogPdf, 'job-log-'.$this->order->job_order_number.'.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($expenseLogPdf, 'expense-log-'.$this->order->job_order_number.'.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}

