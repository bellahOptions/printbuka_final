<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrainingApplicationConfirmationMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public Training $application) {}

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('We received your Printbuka PGTP application'))
            ->view('mail.training.application-confirmation')
            ->with([
                'application' => $this->application,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'training.application_confirmation';
    }

    protected function templateVariables(): array
    {
        return [
            'applicant_name' => trim($this->application->first_name.' '.$this->application->last_name),
            'track' => (string) $this->application->desired_skill,
        ];
    }
}
