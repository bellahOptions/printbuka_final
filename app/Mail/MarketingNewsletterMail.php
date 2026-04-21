<?php

namespace App\Mail;

use App\Models\NewsletterCampaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarketingNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $customer, public NewsletterCampaign $campaign)
    {
    }

    public function build(): self
    {
        return $this
            ->subject($this->campaign->subject)
            ->view('mail.newsletters.campaign')
            ->with([
                'customer' => $this->customer,
                'campaign' => $this->campaign,
            ]);
    }
}

