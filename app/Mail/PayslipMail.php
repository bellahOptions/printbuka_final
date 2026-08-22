<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\PayrollEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayslipMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public readonly PayrollEntry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->templateSubject('Your Payslip — '.$this->entry->payrollRun?->periodLabel().' — Printbuka')
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff.payslip', with: [
            'introHtml' => $this->templateIntroHtml(),
            'outroHtml' => $this->templateOutroHtml(),
        ]);
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('admin.payroll.payslip-pdf', ['entry' => $this->entry]);
        $filename = 'Payslip-'.($this->entry->payrollRun?->periodLabel() ?? 'N/A').'.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }

    protected function templateKey(): string
    {
        return 'payroll.payslip';
    }

    protected function templateVariables(): array
    {
        return [
            'staff_name' => (string) ($this->entry->staff?->displayName() ?? ''),
            'period_label' => (string) ($this->entry->payrollRun?->periodLabel() ?? ''),
            'net_salary' => number_format((float) $this->entry->net_salary, 2),
        ];
    }
}
