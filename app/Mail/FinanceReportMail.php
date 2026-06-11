<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class FinanceReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private Collection $entries,
        private float $incomeTotal,
        private float $expenseTotal,
        private float $netTotal,
        private string $period,
        private ?string $dateFrom,
        private ?string $dateTo,
        private string $generatedByName,
        private string $periodLabel,
    ) {}

    public function build(): self
    {
        $pdf = Pdf::loadView('admin.finance.report-pdf', [
            'entries'      => $this->entries,
            'incomeTotal'  => $this->incomeTotal,
            'expenseTotal' => $this->expenseTotal,
            'netTotal'     => $this->netTotal,
            'period'       => $this->period,
            'dateFrom'     => $this->dateFrom ? \Carbon\Carbon::parse($this->dateFrom) : null,
            'dateTo'       => $this->dateTo   ? \Carbon\Carbon::parse($this->dateTo)   : null,
            'generatedBy'  => null,
        ])->output();

        $filename = 'finance-'.strtolower($this->periodLabel).'-'.now()->format('Y-m-d').'.pdf';

        return $this
            ->subject('Printbuka Finance Report — '.$this->periodLabel.' ('.now()->format('M j, Y').')')
            ->view('mail.finance.report', [
                'periodLabel'     => $this->periodLabel,
                'incomeTotal'     => $this->incomeTotal,
                'expenseTotal'    => $this->expenseTotal,
                'netTotal'        => $this->netTotal,
                'generatedByName' => $this->generatedByName,
            ])
            ->attachData($pdf, $filename, ['mime' => 'application/pdf']);
    }
}
