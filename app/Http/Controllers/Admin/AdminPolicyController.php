<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use App\Models\RefundPolicy;
use App\Models\TermsCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $this->updatePolicy($request, TermsCondition::class, 'Terms & Conditions');

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
    private function updatePolicy(Request $request, string $modelClass, string $defaultTitle): void
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
            'content' => $validated['content'] ?? null,
            'is_published' => $shouldPublish,
            'published_at' => $shouldPublish ? ($policy->published_at ?? now()) : null,
            'updated_by_id' => $request->user()?->id,
        ]);

        if (! $policy->exists) {
            $policy->created_by_id = $request->user()?->id;
        }

        $policy->save();
    }
}
