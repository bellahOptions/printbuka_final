<?php

namespace App\Mail;

use App\Models\PayrollRun;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayrollRunMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly PayrollRun $run,
        public readonly string $sentByName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payroll Summary — '.$this->run->periodLabel().' — Printbuka',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payroll.run');
    }

    public function attachments(): array
    {
        $entries   = $this->run->entries()->with('staff')->get();
        $totalGross       = $entries->sum('gross_salary');
        $totalDeductions  = $entries->sum('total_deductions');
        $totalNet         = $entries->sum('net_salary');

        $pdf = Pdf::loadView('admin.payroll.run-pdf', [
            'run'             => $this->run->load('createdBy', 'finalizedBy'),
            'entries'         => $entries,
            'totalGross'      => $totalGross,
            'totalDeductions' => $totalDeductions,
            'totalNet'        => $totalNet,
        ])->setPaper('a4', 'landscape');

        $filename = 'Payroll-'.$this->run->periodLabel().'.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
