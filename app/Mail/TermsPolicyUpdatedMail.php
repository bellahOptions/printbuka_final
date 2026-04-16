<?php

namespace App\Mail;

use App\Models\TermsCondition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TermsPolicyUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $customer, public TermsCondition $terms)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Important update: Terms & Conditions')
            ->view('mail.policies.terms-updated')
            ->with([
                'customer' => $this->customer,
                'terms' => $this->terms,
                'termsUrl' => route('policies.terms'),
            ]);
    }
}

