<?php

namespace Tests\Feature;

use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyPublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_terms_page_is_visible_to_users(): void
    {
        TermsCondition::query()->create([
            'title' => 'Terms & Conditions',
            'content' => '<h2>Usage Rules</h2><p>Updated terms for all customers.</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get(route('policies.terms'))
            ->assertOk()
            ->assertSee('Terms & Conditions')
            ->assertSee('Updated terms for all customers.');
    }

    public function test_unpublished_privacy_policy_is_not_exposed(): void
    {
        PrivacyPolicy::query()->create([
            'title' => 'Privacy Policy',
            'content' => 'Internal draft should not be publicly visible.',
            'is_published' => false,
        ]);

        $this->get(route('policies.privacy'))
            ->assertOk()
            ->assertDontSee('Internal draft should not be publicly visible.')
            ->assertSee('currently being updated');
    }

    public function test_footer_includes_policy_links_for_users(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('policies.terms'))
            ->assertSee(route('policies.privacy'))
            ->assertSee(route('policies.refund'))
            ->assertDontSee(route('staff.login'));
    }
}
