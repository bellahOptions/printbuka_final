<?php

namespace Tests\Feature;

use App\Mail\TermsPolicyUpdatedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminPolicyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_and_update_terms_policy(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.policies.edit'))
            ->assertOk();

        $this->actingAs($superAdmin)
            ->put(route('admin.policies.terms.update'), [
                'title' => 'Terms & Conditions',
                'content' => 'These are the official terms for Printbuka.',
                'is_published' => 1,
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('terms_conditions', [
            'title' => 'Terms & Conditions',
            'content' => 'These are the official terms for Printbuka.',
            'is_published' => true,
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);
    }

    public function test_non_super_admin_cannot_access_or_update_policy_editor(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.policies.edit'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.policies.privacy.update'), [
                'title' => 'Privacy Policy',
                'content' => 'New privacy policy content.',
                'is_published' => 1,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('privacy_policies', 0);
    }

    public function test_updating_terms_sends_email_notification_to_all_customers(): void
    {
        Mail::fake();

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $customerA = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email' => 'customer-a@example.com',
        ]);

        $customerB = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email' => 'customer-b@example.com',
        ]);

        $inactiveCustomer = User::factory()->create([
            'role' => 'customer',
            'is_active' => false,
            'email' => 'inactive-customer@example.com',
        ]);

        $staff = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'email' => 'staff@example.com',
        ]);

        $this->actingAs($superAdmin)
            ->put(route('admin.policies.terms.update'), [
                'title' => 'Terms & Conditions',
                'content' => 'Updated terms document for all customers.',
                'is_published' => 1,
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        Mail::assertSent(TermsPolicyUpdatedMail::class, 3);
        Mail::assertSent(TermsPolicyUpdatedMail::class, fn (TermsPolicyUpdatedMail $mail): bool => $mail->hasTo($customerA->email));
        Mail::assertSent(TermsPolicyUpdatedMail::class, fn (TermsPolicyUpdatedMail $mail): bool => $mail->hasTo($customerB->email));
        Mail::assertSent(TermsPolicyUpdatedMail::class, fn (TermsPolicyUpdatedMail $mail): bool => $mail->hasTo($inactiveCustomer->email));
        Mail::assertNotSent(TermsPolicyUpdatedMail::class, fn (TermsPolicyUpdatedMail $mail): bool => $mail->hasTo($staff->email));
    }
}
