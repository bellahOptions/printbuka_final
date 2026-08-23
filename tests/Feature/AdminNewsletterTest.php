<?php

namespace Tests\Feature;

use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminNewsletterTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(string $role): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        StaffProfile::query()->create([
            'user_id' => $user->id,
            'kyc_status' => 'approved',
        ]);

        return $user;
    }

    private function campaignBlocks(): string
    {
        return json_encode([
            ['id' => '1', 'type' => 'heading', 'text' => 'Hello {{customer_name}}'],
            ['id' => '2', 'type' => 'paragraph', 'text' => 'Big savings this week.'],
        ]);
    }

    public function test_authorized_staff_can_view_newsletter_pages(): void
    {
        $admin = $this->makeStaff('super_admin');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.newsletters.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.newsletters.create'))
            ->assertOk();
    }

    public function test_non_authorized_role_cannot_access_newsletters(): void
    {
        $staff = $this->makeStaff('office_assistant');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.newsletters.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.newsletters.create'))
            ->assertForbidden();
    }

    public function test_newsletter_can_be_composed_and_sent_to_all_active_customers(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $customerOne = User::factory()->create(['role' => 'customer', 'is_active' => true, 'email_verified_at' => now()]);
        $customerTwo = User::factory()->create(['role' => 'customer', 'is_active' => true, 'email_verified_at' => now()]);
        // Inactive customer must not be counted or emailed.
        User::factory()->create(['role' => 'customer', 'is_active' => false, 'email_verified_at' => now()]);

        $response = $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.newsletters.store'), [
                'subject' => 'Big Print Sale',
                'preheader' => 'Save big this week',
                'blocks' => $this->campaignBlocks(),
            ]);

        $response->assertRedirect();

        $campaign = NewsletterCampaign::query()->firstOrFail();

        $this->assertSame('Big Print Sale', $campaign->subject);
        $this->assertSame('Save big this week', $campaign->preheader);
        $this->assertIsArray($campaign->blocks);
        $this->assertSame(2, $campaign->recipient_count);
        $this->assertSame(2, $campaign->emails_sent);
        $this->assertSame(0, $campaign->emails_failed);
        $this->assertNotNull($campaign->sent_at);

        Mail::assertSent(NewsletterCampaignMail::class, 2);
        Mail::assertSent(NewsletterCampaignMail::class, fn (NewsletterCampaignMail $mail) => $mail->customer->id === $customerOne->id);
        Mail::assertSent(NewsletterCampaignMail::class, fn (NewsletterCampaignMail $mail) => $mail->customer->id === $customerTwo->id);
    }

    public function test_newsletter_content_renders_with_the_recipients_name(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true, 'email_verified_at' => now()]);

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.newsletters.store'), [
                'subject' => 'Personalized offer',
                'blocks' => $this->campaignBlocks(),
            ]);

        Mail::assertSent(NewsletterCampaignMail::class, function (NewsletterCampaignMail $mail) use ($customer): bool {
            $html = $mail->render();

            return str_contains($html, 'Hello '.$customer->displayName())
                && str_contains($html, 'Big savings this week.');
        });
    }

    public function test_invalid_block_data_is_rejected(): void
    {
        $admin = $this->makeStaff('super_admin');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.newsletters.store'), [
                'subject' => 'Bad blocks',
                'blocks' => 'not-json',
            ])
            ->assertStatus(422);

        $this->assertSame(0, NewsletterCampaign::query()->count());
    }

    public function test_preview_endpoint_reflects_unsaved_block_edits_live(): void
    {
        $admin = $this->makeStaff('super_admin');

        $blocks = json_encode([
            ['id' => '1', 'type' => 'paragraph', 'text' => 'LIVE-PREVIEW-MARKER-not-yet-saved'],
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.newsletters.preview').'?'.http_build_query([
                'blocks' => $blocks,
            ]));

        $response->assertOk();
        $response->assertSee('LIVE-PREVIEW-MARKER-not-yet-saved');

        // Confirms nothing was persisted by merely previewing.
        $this->assertSame(0, NewsletterCampaign::query()->count());
    }
}
