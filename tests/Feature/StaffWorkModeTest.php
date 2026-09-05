<?php

namespace Tests\Feature;

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffWorkModeTest extends TestCase
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

    public function test_staff_can_declare_a_fully_remote_work_mode(): void
    {
        $staff = $this->makeStaff('office_assistant');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.work-mode.update'), ['work_mode' => 'remote'])
            ->assertRedirect();

        $profile = $staff->staffProfile()->firstOrFail();
        $this->assertSame('remote', $profile->work_mode);
        $this->assertNull($profile->onsite_days);
        $this->assertNotNull($profile->work_mode_set_at);
        $this->assertFalse($profile->fresh()->needsWorkModePrompt());
    }

    public function test_hybrid_work_mode_requires_at_least_one_onsite_day(): void
    {
        $staff = $this->makeStaff('office_assistant');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.work-mode.update'), ['work_mode' => 'hybrid'])
            ->assertSessionHasErrors('onsite_days');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.work-mode.update'), [
                'work_mode' => 'hybrid',
                'onsite_days' => ['Mon', 'Wed', 'Fri'],
            ])
            ->assertRedirect();

        $profile = $staff->staffProfile()->firstOrFail();
        $this->assertSame('hybrid', $profile->work_mode);
        $this->assertSame(['Mon', 'Wed', 'Fri'], $profile->onsite_days);
    }

    public function test_staff_cannot_change_a_previously_declared_work_mode(): void
    {
        $staff = $this->makeStaff('office_assistant');
        $staff->staffProfile()->update(['work_mode' => 'onsite', 'work_mode_set_at' => now()]);

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.work-mode.update'), ['work_mode' => 'remote'])
            ->assertRedirect()
            ->assertSessionHas('status_error');

        $this->assertSame('onsite', $staff->staffProfile()->firstOrFail()->work_mode);
    }

    public function test_super_admin_can_override_a_locked_work_mode(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $staff = $this->makeStaff('office_assistant');
        $staff->staffProfile()->update(['work_mode' => 'onsite', 'work_mode_set_at' => now()]);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.work-mode.override', $staff), ['work_mode' => 'remote'])
            ->assertRedirect();

        $this->assertSame('remote', $staff->staffProfile()->firstOrFail()->work_mode);
    }

    public function test_non_super_admin_cannot_override_a_locked_work_mode(): void
    {
        $hr = $this->makeStaff('hr');
        $staff = $this->makeStaff('office_assistant');
        $staff->staffProfile()->update(['work_mode' => 'onsite', 'work_mode_set_at' => now()]);

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.work-mode.override', $staff), ['work_mode' => 'remote'])
            ->assertForbidden();

        $this->assertSame('onsite', $staff->staffProfile()->firstOrFail()->work_mode);
    }
}
