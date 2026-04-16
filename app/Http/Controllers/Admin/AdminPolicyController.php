<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TermsPolicyUpdatedMail;
use App\Models\PrivacyPolicy;
use App\Models\RefundPolicy;
use App\Models\TermsCondition;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminPolicyController extends Controller
{
    public function edit(): View
    {
        return view('admin.policies.edit', [
            'terms' => TermsCondition::query()->latest('id')->first() ?? new TermsCondition(['title' => 'Terms & Conditions']),
            'privacy' => PrivacyPolicy::query()->latest('id')->first() ?? new PrivacyPolicy(['title' => 'Privacy Policy']),
            'refund' => RefundPolicy::query()->latest('id')->first() ?? new RefundPolicy(['title' => 'Refund Policy']),
        ]);
    }

    public function updateTerms(Request $request): RedirectResponse
    {
        /** @var TermsCondition $policy */
        $policy = $this->updatePolicy($request, TermsCondition::class, 'Terms & Conditions');

        if ($policy->wasRecentlyCreated || $policy->wasChanged(['title', 'content', 'is_published', 'published_at'])) {
            $this->notifyCustomersAboutTermsUpdate($policy);
        }

        return back()->with('status', 'Terms & Conditions updated.');
    }

    public function updatePrivacy(Request $request): RedirectResponse
    {
        $this->updatePolicy($request, PrivacyPolicy::class, 'Privacy Policy');

        return back()->with('status', 'Privacy Policy updated.');
    }

    public function updateRefund(Request $request): RedirectResponse
    {
        $this->updatePolicy($request, RefundPolicy::class, 'Refund Policy');

        return back()->with('status', 'Refund Policy updated.');
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function updatePolicy(Request $request, string $modelClass, string $defaultTitle): Model
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        /** @var Model&object{created_by_id:int|null,published_at:mixed} $policy */
        $policy = $modelClass::query()->latest('id')->first() ?? new $modelClass();

        $shouldPublish = $request->boolean('is_published');

        $policy->fill([
            'title' => trim((string) ($validated['title'] ?? '')) !== '' ? $validated['title'] : $defaultTitle,
            'content' => $this->sanitizePolicyContent($validated['content'] ?? null),
            'is_published' => $shouldPublish,
            'published_at' => $shouldPublish ? ($policy->published_at ?? now()) : null,
            'updated_by_id' => $request->user()?->id,
        ]);

        if (! $policy->exists) {
            $policy->created_by_id = $request->user()?->id;
        }

        $policy->save();

        return $policy;
    }

    private function sanitizePolicyContent(?string $content): ?string
    {
        $value = trim((string) $content);

        if ($value === '') {
            return null;
        }

        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><blockquote><a>';
        $cleaned = strip_tags($value, $allowedTags);
        $cleaned = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script>/is', '', $cleaned) ?? '';
        $cleaned = preg_replace('/<\s*style[^>]*>.*?<\s*\/\s*style>/is', '', $cleaned) ?? '';
        $cleaned = preg_replace('/\son\w+="[^"]*"/i', '', $cleaned) ?? '';
        $cleaned = preg_replace("/\son\w+='[^']*'/i", '', $cleaned) ?? '';
        $cleaned = preg_replace('/\shref="javascript:[^"]*"/i', ' href="#"', $cleaned) ?? '';
        $cleaned = preg_replace("/\shref='javascript:[^']*'/i", " href='#'", $cleaned) ?? '';

        return trim($cleaned) !== '' ? trim($cleaned) : null;
    }

    private function notifyCustomersAboutTermsUpdate(TermsCondition $policy): void
    {
        User::query()
            ->where('role', 'customer')
            ->whereNotNull('email')
            ->orderBy('id')
            ->chunkById(200, function ($customers) use ($policy): void {
                foreach ($customers as $customer) {
                    if (! filled($customer->email)) {
                        continue;
                    }

                    try {
                        Mail::to($customer->email)->send(new TermsPolicyUpdatedMail($customer, $policy));
                    } catch (\Throwable $exception) {
                        Log::error('Terms update email failed.', [
                            'terms_id' => $policy->id,
                            'customer_id' => $customer->id,
                            'customer_email' => $customer->email,
                            'message' => $exception->getMessage(),
                        ]);
                    }
                }
            });
    }
}
