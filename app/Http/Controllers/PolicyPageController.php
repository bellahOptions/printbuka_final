<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use App\Models\RefundPolicy;
use App\Models\TermsCondition;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class PolicyPageController extends Controller
{
    public function terms(): View
    {
        return $this->renderPolicyPage(
            TermsCondition::class,
            'Terms & Conditions',
            'Clear terms for using Printbuka products, services, and order workflows.'
        );
    }

    public function privacy(): View
    {
        return $this->renderPolicyPage(
            PrivacyPolicy::class,
            'Privacy Policy',
            'How Printbuka collects, stores, and protects your personal and order information.'
        );
    }

    public function refund(): View
    {
        return $this->renderPolicyPage(
            RefundPolicy::class,
            'Refund Policy',
            'Refund, cancellation, and adjustment rules for Printbuka purchases and services.'
        );
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function renderPolicyPage(string $modelClass, string $fallbackTitle, string $pageSummary): View
    {
        /** @var Model&object{title:string|null,content:string|null,is_published:bool,published_at:mixed,updated_at:mixed}|null $policy */
        $policy = $modelClass::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->latest('id')
            ->first();

        return view('policies.show', [
            'title' => $policy?->title ?: $fallbackTitle,
            'summary' => $pageSummary,
            'policy' => $policy,
        ]);
    }
}

