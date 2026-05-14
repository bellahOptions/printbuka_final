<?php

namespace App\Mail;

use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrainingApplicationDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Training $application) {}

    public function build(): self
    {
        $subject = $this->application->status === Training::STATUS_ACCEPTED
            ? 'Your Printbuka PGTP application was accepted'
            : 'Update on your Printbuka PGTP application';

        return $this
            ->subject($subject)
            ->view('mail.training.application-decision')
            ->with(['application' => $this->application]);
    }
}
