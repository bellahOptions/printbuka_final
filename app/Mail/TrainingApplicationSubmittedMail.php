<?php

namespace App\Mail;

use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrainingApplicationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Training $application) {}

    public function build(): self
    {
        return $this
            ->subject('New PGTP application: '.$this->application->first_name.' '.$this->application->last_name)
            ->view('mail.training.application-submitted')
            ->with(['application' => $this->application]);
    }
}
