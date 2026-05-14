<?php

namespace App\Mail;

use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrainingApplicationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Training $application) {}

    public function build(): self
    {
        return $this
            ->subject('We received your Printbuka PGTP application')
            ->view('mail.training.application-confirmation')
            ->with(['application' => $this->application]);
    }
}
