<?php

namespace Tests\Feature;

use App\Models\StaffProfile;
use App\Models\User;
use App\Notifications\StaffPushNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminStaffSecondaryRoleTest extends TestCase
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

    public function test_super_admin_can_elevate_a_staff_member_with_an_additional_role(): void
    {
        Notification::fake();

        $superAdmin = $this->makeStaff('super_admin');
        $designer = $this->makeStaff('designer');

        $response = $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.secondary-role.update', $designer), [
                'secondary_role' => 'operations_manager',
            ]);

        $response->assertRedirect();

        $designer->refresh();
        $this->assertSame('operations_manager', $designer->secondary_role);
        $this->assertTrue($designer->canAdmin('workflow.approve'));

        Notification::assertSentTo(
            $designer,
            StaffPushNotification::class,
            fn (StaffPushNotification $notification): bool => $notification->type === 'role_elevated'
        );
    }

    public function test_removing_the_secondary_role_notifies_staff(): void
    {
        Notification::fake();

        $superAdmin = $this->makeStaff('super_admin');
        $designer = $this->makeStaff('designer');
        $designer->forceFill(['secondary_role' => 'operations_manager'])->save();

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.secondary-role.update', $designer), [
                'secondary_role' => '',
            ])
            ->assertRedirect();

        $designer->refresh();
        $this->assertNull($designer->secondary_role);

        Notification::assertSentTo(
            $designer,
            StaffPushNotification::class,
            fn (StaffPushNotification $notification): bool => $notification->type === 'role_elevation_removed'
        );
    }

    public function test_non_super_admin_cannot_elevate_staff_roles(): void
    {
        $hr = $this->makeStaff('hr');
        $designer = $this->makeStaff('designer');

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.secondary-role.update', $designer), [
                'secondary_role' => 'operations_manager',
            ])
            ->assertForbidden();

        $this->assertNull($designer->fresh()->secondary_role);
    }

    public function test_cannot_assign_super_admin_as_a_secondary_role(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $designer = $this->makeStaff('designer');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.secondary-role.update', $designer), [
                'secondary_role' => 'super_admin',
            ])
            ->assertSessionHasErrors('secondary_role');
    }
}
