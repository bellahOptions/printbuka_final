<?php

namespace App\Mail;

use App\Models\NewsletterCampaign;
use App\Models\User;
use App\Support\EmailBlockRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $customer, public NewsletterCampaign $campaign) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->campaign->subject);
    }

    public function content(): Content
    {
        $bodyHtml = EmailBlockRenderer::render($this->campaign->blocks ?? [], [
            'customer_name' => $this->customer->displayName(),
            'company_name' => (string) (\App\Support\SiteSettings::all()['site_name'] ?? config('app.name', 'Printbuka')),
        ]);

        return new Content(view: 'mail.newsletters.campaign', with: [
            'campaign' => $this->campaign,
            'customer' => $this->customer,
            'bodyHtml' => $bodyHtml,
        ]);
    }
}
