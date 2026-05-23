<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobFinanceSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing(['invoice', 'product']);
    }

    public function build(): self
    {
        $expenseEntries = $this->order->financeEntries()
            ->where('type', 'expense')
            ->orderByDesc('entry_date')
            ->get();

        $pdf = Pdf::loadView('admin.orders.job-log-pdf', [
            'order' => $this->order,
            'expenseEntries' => $expenseEntries,
            'asPdf' => true,
        ])->output();

        return $this
            ->subject('Printbuka Job Finance Summary · '.$this->order->job_order_number)
            ->view('mail.jobs.finance-summary', [
                'order' => $this->order,
                'expenseEntries' => $expenseEntries,
            ])
            ->attachData($pdf, 'job-finance-summary-'.$this->order->job_order_number.'.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
