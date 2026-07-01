<?php

namespace App\Mail;

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
    use Queueable, SerializesModels;

    public function __construct(public readonly PayrollEntry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Payslip — '.$this->entry->payrollRun?->periodLabel().' — Printbuka'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff.payslip');
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
}
