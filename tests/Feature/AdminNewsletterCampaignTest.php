<?php

namespace Tests\Feature;

use App\Mail\MarketingNewsletterMail;
use App\Models\NewsletterCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminNewsletterCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_service_staff_can_access_newsletter_management_page(): void
    {
        $staff = $this->adminUser('customer_service');

        $this->actingAs($staff)
            ->get(route('admin.newsletters.index'))
            ->assertOk()
            ->assertSee('Newsletter Campaigns');
    }

    public function test_marketing_staff_can_send_newsletter_to_registered_customers(): void
    {
        Mail::fake();

        $staff = $this->adminUser('marketing');
        $activeCustomerOne = $this->customer('customer.one@example.com', true, true);
        $activeCustomerTwo = $this->customer('customer.two@example.com', true, true);
        $inactiveCustomer = $this->customer('inactive.customer@example.com', false, true);
        $unverifiedCustomer = $this->customer('unverified.customer@example.com', true, false);
        $staffUser = $this->adminUser('operations');

        $this->actingAs($staff)
            ->post(route('admin.newsletters.store'), [
                'subject' => 'April Promo Campaign',
                'preheader' => 'Special discount for loyal customers',
                'headline' => 'Upgrade your brand this week',
                'message' => "Enjoy discounted rates on selected print products.\nOffer ends this Friday.",
                'cta_label' => 'Shop Offers',
                'cta_url' => 'https://printbuka.com/products',
            ])
            ->assertRedirect(route('admin.newsletters.index'))
            ->assertSessionHas('status');

        $campaign = NewsletterCampaign::query()->latest('id')->firstOrFail();

        $this->assertSame('April Promo Campaign', $campaign->subject);
        $this->assertSame(2, $campaign->recipient_count);
        $this->assertSame(2, $campaign->emails_sent);
        $this->assertSame(0, $campaign->emails_failed);
        $this->assertNotNull($campaign->sent_at);

        Mail::assertSent(MarketingNewsletterMail::class, 2);
        Mail::assertSent(MarketingNewsletterMail::class, function (MarketingNewsletterMail $mail) use ($activeCustomerOne): bool {
            return $mail->hasTo($activeCustomerOne->email);
        });
        Mail::assertSent(MarketingNewsletterMail::class, function (MarketingNewsletterMail $mail) use ($activeCustomerTwo): bool {
            return $mail->hasTo($activeCustomerTwo->email);
        });
        Mail::assertNotSent(MarketingNewsletterMail::class, function (MarketingNewsletterMail $mail) use ($inactiveCustomer): bool {
            return $mail->hasTo($inactiveCustomer->email);
        });
        Mail::assertNotSent(MarketingNewsletterMail::class, function (MarketingNewsletterMail $mail) use ($unverifiedCustomer): bool {
            return $mail->hasTo($unverifiedCustomer->email);
        });
        Mail::assertNotSent(MarketingNewsletterMail::class, function (MarketingNewsletterMail $mail) use ($staffUser): bool {
            return $mail->hasTo($staffUser->email);
        });
    }

    public function test_staff_without_permission_cannot_send_newsletter(): void
    {
        $staff = $this->adminUser('operations');

        $this->actingAs($staff)
            ->post(route('admin.newsletters.store'), [
                'subject' => 'Restricted Campaign',
                'message' => 'This should not be sent.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('newsletter_campaigns', 0);
    }

    private function adminUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function customer(string $email, bool $active, bool $verified): User
    {
        return User::factory()->create([
            'role' => 'customer',
            'is_active' => $active,
            'email' => $email,
            'email_verified_at' => $verified ? now() : null,
        ]);
    }
}

