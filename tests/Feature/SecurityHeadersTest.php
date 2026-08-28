<?php

namespace Tests\Feature;

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The global Permissions-Policy header previously denied geolocation
 * outright (geolocation=()), which silently blocked navigator.geolocation
 * in every browser before any client-side code could run — including on
 * the staff attendance page, which needs it to verify staff are on site.
 * Scoped to (self) instead: same-origin pages can still request it, but a
 * third-party/cross-origin embed still can't.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissions_policy_allows_geolocation_for_same_origin_pages(): void
    {
        $response = $this->get(route('home'));

        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
    }

    public function test_permissions_policy_allows_geolocation_on_the_attendance_page(): void
    {
        $staff = User::factory()->create([
            'role' => 'office_assistant',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);
        StaffProfile::query()->create(['user_id' => $staff->id, 'kyc_status' => 'approved']);

        $response = $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.attendance.index'));

        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
    }
}
