<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSurfaceIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_is_redirected_from_customer_dashboard_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_user_is_redirected_from_customer_profile_to_admin_profile(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('profile.edit'))
            ->assertRedirect(route('admin.profile.edit'));
    }

    public function test_admin_user_cannot_update_profile_through_customer_profile_endpoint(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('profile.update'), [
                'first_name' => 'Updated',
                'last_name' => 'Admin',
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $this->assertNotEquals('Updated', $admin->fresh()->first_name);
    }
}

