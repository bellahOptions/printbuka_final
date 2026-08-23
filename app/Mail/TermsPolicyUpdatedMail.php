<?php

namespace App\Mail;

use App\Mail\Concerns\HasEditableTemplate;
use App\Models\TermsCondition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TermsPolicyUpdatedMail extends Mailable
{
    use HasEditableTemplate, Queueable, SerializesModels;

    public function __construct(public User $customer, public TermsCondition $terms)
    {
    }

    public function build(): self
    {
        return $this
            ->subject($this->templateSubject('Important update: Terms & Conditions'))
            ->view('mail.policies.terms-updated')
            ->with([
                'customer' => $this->customer,
                'terms' => $this->terms,
                'termsUrl' => route('policies.terms'),
                'introHtml' => $this->templateIntroHtml(),
                'outroHtml' => $this->templateOutroHtml(),
            ]);
    }

    protected function templateKey(): string
    {
        return 'policy.terms_updated';
    }

    protected function templateVariables(): array
    {
        return [
            'customer_name' => $this->customer->displayName(),
            'updated_at' => optional($this->terms->updated_at)->format('M d, Y') ?? '',
        ];
    }
}

