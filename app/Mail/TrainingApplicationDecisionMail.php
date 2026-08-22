<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrainingApplicationDecisionMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public Training $application) {}

    public function build(): self
    {
        $subject = $this->application->status === Training::STATUS_ACCEPTED
            ? 'Your Printbuka PGTP application was accepted'
            : 'Update on your Printbuka PGTP application';

        return $this
            ->subject($this->templateSubject($subject))
            ->view('mail.training.application-decision')
            ->with([
                'application' => $this->application,
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'training.application_decision';
    }

    protected function templateVariables(): array
    {
        return [
            'applicant_name' => $this->application->fullName(),
            'track' => (string) $this->application->desired_skill,
            'decision' => (string) $this->application->statusLabel(),
        ];
    }
}
