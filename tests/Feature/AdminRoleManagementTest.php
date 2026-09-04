<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(string $role, array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ], $overrides));
    }

    public function test_super_admin_can_create_a_custom_role_and_it_grants_the_chosen_permissions(): void
    {
        $superAdmin = $this->makeStaff('super_admin');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.roles.store'), [
                'slug' => 'video_editor',
                'label' => 'Video Editor',
                'priority' => 35,
                'dashboard_menu' => 'Job Briefs, Uploads',
                'permissions' => ['admin.view', 'orders.view', 'design.upload'],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('roles', ['slug' => 'video_editor', 'label' => 'Video Editor', 'is_system' => false]);

        $editor = $this->makeStaff('video_editor');

        $this->assertTrue($editor->canAdmin('design.upload'));
        $this->assertTrue($editor->canAdmin('orders.view'));
        $this->assertFalse($editor->canAdmin('invoices.manage'));
        $this->assertTrue($editor->hasAdminAccess());
    }

    public function test_non_super_admin_cannot_manage_roles(): void
    {
        $hr = $this->makeStaff('hr');

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff.roles.index'))
            ->assertForbidden();

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.roles.store'), [
                'slug' => 'sneaky',
                'label' => 'Sneaky',
                'priority' => 90,
                'permissions' => ['*'],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('roles', ['slug' => 'sneaky']);
    }

    public function test_super_admin_can_edit_an_existing_role_and_change_takes_effect_immediately(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $designerRole = Role::query()->where('slug', 'designer')->firstOrFail();
        $designer = $this->makeStaff('designer');

        $this->assertFalse($designer->canAdmin('finance.view'));

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.roles.update', $designerRole), [
                'label' => $designerRole->label,
                'priority' => $designerRole->priority,
                'permissions' => array_merge($designerRole->permissions, ['finance.view']),
            ])
            ->assertRedirect();

        $this->assertTrue($designer->fresh()->canAdmin('finance.view'));
    }

    public function test_super_admin_role_permissions_cannot_be_stripped_via_the_edit_form(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $superAdminRole = Role::query()->where('slug', 'super_admin')->firstOrFail();

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.roles.update', $superAdminRole), [
                'label' => 'Process & Technology Manager',
                'priority' => 100,
                'permissions' => ['orders.view'],
            ])
            ->assertRedirect();

        $this->assertSame(['*'], $superAdminRole->fresh()->permissions);
    }

    public function test_system_roles_cannot_be_deleted(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $officeAssistantRole = Role::query()->where('slug', 'office_assistant')->firstOrFail();

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.staff.roles.destroy', $officeAssistantRole))
            ->assertRedirect();

        $this->assertDatabaseHas('roles', ['slug' => 'office_assistant']);
    }

    public function test_custom_role_cannot_be_deleted_while_staff_hold_it_but_can_once_reassigned(): void
    {
        $superAdmin = $this->makeStaff('super_admin');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.roles.store'), [
                'slug' => 'courier',
                'label' => 'Courier',
                'priority' => 15,
                'permissions' => ['admin.view', 'delivery.update'],
            ]);

        $courierRole = Role::query()->where('slug', 'courier')->firstOrFail();
        $courierStaff = $this->makeStaff('courier');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.staff.roles.destroy', $courierRole))
            ->assertRedirect();
        $this->assertDatabaseHas('roles', ['slug' => 'courier']);

        $courierStaff->update(['role' => 'office_assistant']);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.staff.roles.destroy', $courierRole))
            ->assertRedirect();
        $this->assertDatabaseMissing('roles', ['slug' => 'courier']);
    }

    public function test_super_admin_can_change_a_staff_members_role_and_department(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $staffMember = $this->makeStaff('office_assistant', ['department' => null]);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.update', $staffMember), [
                'role' => 'customer_service',
                'department' => 'Client Services',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $staffMember->refresh();
        $this->assertSame('customer_service', $staffMember->role);
        $this->assertSame('Client Services', $staffMember->department);
    }
}
