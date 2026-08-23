<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Support\PdfTemplateOverrides;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class FinanceReportMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

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
            ...PdfTemplateOverrides::forKey('pdf.finance_report', $this->templateVariables()),
        ])->output();

        $filename = 'finance-'.strtolower($this->periodLabel).'-'.now()->format('Y-m-d').'.pdf';

        return $this
            ->subject($this->templateSubject('Printbuka Finance Report — '.$this->periodLabel.' ('.now()->format('M j, Y').')'))
            ->view('mail.finance.report', [
                'periodLabel'     => $this->periodLabel,
                'incomeTotal'     => $this->incomeTotal,
                'expenseTotal'    => $this->expenseTotal,
                'netTotal'        => $this->netTotal,
                'generatedByName' => $this->generatedByName,
                'introHtml'       => $this->templateIntroHtml(),
                'outroHtml'       => $this->templateOutroHtml(),
            ])
            ->attachData($pdf, $filename, ['mime' => 'application/pdf']);
    }

    protected function templateKey(): string
    {
        return 'finance.report';
    }

    protected function templateVariables(): array
    {
        return [
            'period_label' => $this->periodLabel,
            'income_total' => number_format($this->incomeTotal, 2),
            'expense_total' => number_format($this->expenseTotal, 2),
            'net_total' => number_format($this->netTotal, 2),
            'generated_by_name' => $this->generatedByName,
        ];
    }
}
