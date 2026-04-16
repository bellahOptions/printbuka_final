<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
