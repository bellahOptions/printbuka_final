<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminNewsletterController extends Controller
{
    public function index(): View
    {
        $campaigns = NewsletterCampaign::query()
            ->with('sender')
            ->latest('id')
            ->paginate(15);

        $audienceCount = User::query()
            ->where('role', 'customer')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->count();

        return view('admin.newsletters.index', [
            'campaigns' => $campaigns,
            'audienceCount' => $audienceCount,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'preheader' => ['nullable', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:2048', 'regex:/^https?:\/\//i'],
        ]);

        $customers = User::query()
            ->where('role', 'customer')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();

        $campaign = NewsletterCampaign::query()->create([
            'created_by_id' => $request->user()?->id,
            'subject' => $validated['subject'],
            'preheader' => $validated['preheader'] ?? null,
            'headline' => $validated['headline'] ?? null,
            'message' => $validated['message'],
            'cta_label' => $validated['cta_label'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'recipient_count' => $customers->count(),
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($customers as $customer) {
            try {
                Mail::to($customer->email)->send(new NewsletterCampaignMail($customer, $campaign));
                $sent++;
            } catch (\Throwable $exception) {
                $failed++;

                Log::error('Newsletter campaign email failed.', [
                    'campaign_id' => $campaign->id,
                    'customer_id' => $customer->id,
                    'customer_email' => $customer->email,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $campaign->update([
            'emails_sent' => $sent,
            'emails_failed' => $failed,
            'sent_at' => now(),
        ]);

        return back()->with('status', 'Newsletter sent to '.$sent.' of '.$customers->count().' recipient(s).');
    }
}
