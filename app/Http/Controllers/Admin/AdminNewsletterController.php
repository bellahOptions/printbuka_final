<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MarketingNewsletterMail;
use App\Models\NewsletterCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminNewsletterController extends Controller
{
    public function index(): View
    {
        return view('admin.newsletters.index', [
            'campaigns' => NewsletterCampaign::query()
                ->with('sender')
                ->latest()
                ->paginate(15),
            'audienceCount' => $this->newsletterAudienceQuery()->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'preheader' => ['nullable', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:12000'],
            'cta_label' => ['nullable', 'string', 'max:80', 'required_with:cta_url'],
            'cta_url' => ['nullable', 'url:http,https', 'max:2048', 'required_with:cta_label'],
        ]);

        $campaign = NewsletterCampaign::query()->create([
            ...$validated,
            'created_by_id' => $request->user()?->id,
            'recipient_count' => 0,
            'emails_sent' => 0,
            'emails_failed' => 0,
            'sent_at' => null,
        ]);

        $sent = 0;
        $failed = 0;

        $this->newsletterAudienceQuery()
            ->orderBy('id')
            ->chunkById(200, function ($customers) use (&$sent, &$failed, $campaign): void {
                foreach ($customers as $customer) {
                    if (! filled($customer->email)) {
                        continue;
                    }

                    try {
                        Mail::to($customer->email)->send(new MarketingNewsletterMail($customer, $campaign));
                        $sent++;
                    } catch (\Throwable $exception) {
                        $failed++;
                        Log::error('Newsletter email failed.', [
                            'campaign_id' => $campaign->id,
                            'customer_id' => $customer->id,
                            'customer_email' => $customer->email,
                            'message' => $exception->getMessage(),
                        ]);
                    }
                }
            });

        $campaign->update([
            'recipient_count' => $sent + $failed,
            'emails_sent' => $sent,
            'emails_failed' => $failed,
            'sent_at' => now(),
        ]);

        return redirect()
            ->route('admin.newsletters.index')
            ->with('status', "Newsletter sent. {$sent} delivered, {$failed} failed.");
    }

    private function newsletterAudienceQuery(): Builder
    {
        return User::query()
            ->where('role', 'customer')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->whereNotNull('email_verified_at');
    }
}

