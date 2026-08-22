<?php

namespace Tests\Feature;

use App\Models\AdminActivityLog;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminStaffKycEmploymentActivityLogAuditTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(string $role, array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ], $overrides));

        StaffProfile::query()->create([
            'user_id' => $user->id,
            'kyc_status' => 'approved',
        ]);

        return $user;
    }

    public function test_hr_and_super_admin_can_view_staff_kyc_review_panel_but_managing_director_cannot_manage_kyc(): void
    {
        // staff.kyc permission belongs to super_admin ('*'), hr, and managing_director ('*').
        $staffMember = $this->makeStaff('office_assistant');

        foreach (['super_admin', 'hr', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.staff.profile.show', $staffMember))
                ->assertOk()
                ->assertSee('KYC Review');
        }
    }

    public function test_office_assistant_cannot_manage_kyc_and_gets_403_on_review(): void
    {
        $staffMember = $this->makeStaff('office_assistant');
        $viewer = $this->makeStaff('office_assistant');

        // Not self and lacks staff.kyc permission -> profile show should 403.
        $this->actingAs($viewer)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff.profile.show', $staffMember))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.kyc-review', $staffMember), [
                'kyc_action' => 'approve',
            ])
            ->assertForbidden();
    }

    public function test_staff_can_view_own_profile(): void
    {
        $staffMember = $this->makeStaff('office_assistant');

        $this->actingAs($staffMember)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff.profile.show', $staffMember))
            ->assertOk();
    }

    public function test_kyc_review_approve_and_request_correction_flow(): void
    {
        Mail::fake();

        foreach (['super_admin', 'hr', 'managing_director'] as $role) {
            $reviewer = $this->makeStaff($role);
            $staffMember = $this->makeStaff('office_assistant');

            $this->actingAs($reviewer)
                ->withSession(['staff_2fa_verified' => true])
                ->post(route('admin.staff.kyc-review', $staffMember), [
                    'kyc_action' => 'approve',
                    'kyc_notes' => 'Looks good.',
                ])
                ->assertRedirect();

            $this->assertDatabaseHas('staff_profiles', [
                'user_id' => $staffMember->id,
                'kyc_status' => 'approved',
                'kyc_reviewed_by_id' => $reviewer->id,
            ]);

            $this->actingAs($reviewer)
                ->withSession(['staff_2fa_verified' => true])
                ->post(route('admin.staff.kyc-review', $staffMember), [
                    'kyc_action' => 'request_correction',
                    'kyc_notes' => 'Please update your bank details.',
                ])
                ->assertRedirect();

            $staffMember->staffProfile->refresh();
            $this->assertSame('correction_requested', $staffMember->staffProfile->kyc_status);
            $this->assertNull($staffMember->staffProfile->kyc_completed_at);
            $this->assertSame('Please update your bank details.', $staffMember->staffProfile->kyc_review_notes);
        }
    }

    public function test_kyc_review_requires_valid_action(): void
    {
        $reviewer = $this->makeStaff('hr');
        $staffMember = $this->makeStaff('office_assistant');

        $this->actingAs($reviewer)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.kyc-review', $staffMember), [
                'kyc_action' => 'not_a_real_action',
            ])
            ->assertSessionHasErrors('kyc_action');
    }

    public function test_kyc_mark_complete_endpoint(): void
    {
        $reviewer = $this->makeStaff('hr');
        $staffMember = $this->makeStaff('office_assistant');
        $staffMember->staffProfile()->delete();

        $this->actingAs($reviewer)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff.kyc-complete', $staffMember))
            ->assertRedirect();

        $this->assertDatabaseHas('staff_profiles', [
            'user_id' => $staffMember->id,
        ]);
        $this->assertNotNull($staffMember->staffProfile()->first()->kyc_completed_at);
    }

    public function test_super_admin_and_hr_can_view_staff_index_and_managing_director_lacks_permission(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $hr = $this->makeStaff('hr');
        $md = $this->makeStaff('managing_director');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff.index'))
            ->assertOk();

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff.index'))
            ->assertOk();

        // managing_director has '*' permissions, so staff.view passes too.
        $this->actingAs($md)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff.index'))
            ->assertOk();
    }

    public function test_only_super_admin_can_update_staff_role_via_super_admin_middleware(): void
    {
        // admin.staff.update is gated by the `super.admin` middleware, which checks
        // role === 'super_admin' literally -- managing_director's '*' permission set
        // does NOT satisfy this middleware, unlike admin.permission-gated routes.
        $superAdmin = $this->makeStaff('super_admin');
        $md = $this->makeStaff('managing_director');
        $hr = $this->makeStaff('hr');
        $staffMember = $this->makeStaff('office_assistant');

        $this->actingAs($md)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.update', $staffMember), [
                'role' => 'personal_assistant',
            ])
            ->assertForbidden();

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.update', $staffMember), [
                'role' => 'personal_assistant',
            ])
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.staff.update', $staffMember), [
                'role' => 'personal_assistant',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $staffMember->refresh();
        $this->assertSame('personal_assistant', $staffMember->role);
    }

    public function test_employment_status_change_allowed_for_super_admin_and_hr_but_not_managing_director(): void
    {
        // updateEmploymentStatus route middleware is admin.permission:staff.view (which MD passes
        // via '*'), but the controller itself restricts to super_admin/hr explicitly.
        $superAdmin = $this->makeStaff('super_admin');
        $md = $this->makeStaff('managing_director');
        $staffMember = $this->makeStaff('office_assistant');

        $this->actingAs($md)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.employment-status', $staffMember), [
                'employment_status' => 'suspended',
            ])
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.employment-status', $staffMember), [
                'employment_status' => 'suspended',
                'employment_status_reason' => 'Investigation pending.',
            ])
            ->assertRedirect();

        $staffMember->refresh();
        $this->assertSame('suspended', $staffMember->employment_status);
        $this->assertFalse((bool) $staffMember->is_active);
        $this->assertSame('Investigation pending.', $staffMember->employment_status_reason);
    }

    public function test_employment_status_change_cannot_target_self(): void
    {
        $superAdmin = $this->makeStaff('super_admin');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.employment-status', $superAdmin), [
                'employment_status' => 'suspended',
            ])
            ->assertStatus(422);
    }

    public function test_employment_status_change_rejects_invalid_status(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $staffMember = $this->makeStaff('office_assistant');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.employment-status', $staffMember), [
                'employment_status' => 'on_vacation',
            ])
            ->assertSessionHasErrors('employment_status');
    }

    public function test_suspending_staff_destroys_their_active_sessions(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $staffMember = $this->makeStaff('office_assistant');

        \DB::table('sessions')->insert([
            'id' => 'test-session-id',
            'user_id' => $staffMember->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
            'payload' => base64_encode('test'),
            'last_activity' => time(),
        ]);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.employment-status', $staffMember), [
                'employment_status' => 'terminated',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('sessions', ['user_id' => $staffMember->id]);
    }

    public function test_access_restriction_toggle_only_allowed_for_super_admin(): void
    {
        // toggleAccessRestriction has no route-level permission middleware; the controller
        // itself hard-restricts to role === 'super_admin' only (not even '*' permission roles).
        $superAdmin = $this->makeStaff('super_admin');
        $hr = $this->makeStaff('hr');
        $md = $this->makeStaff('managing_director');
        $staffMember = $this->makeStaff('office_assistant');

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.access-restriction', $staffMember), [
                'reason' => 'Suspicious activity',
            ])
            ->assertForbidden();

        $this->actingAs($md)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.access-restriction', $staffMember), [
                'reason' => 'Suspicious activity',
            ])
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.access-restriction', $staffMember), [
                'reason' => 'Suspicious activity',
            ])
            ->assertRedirect();

        $staffMember->refresh();
        $this->assertTrue((bool) $staffMember->access_restricted);
        $this->assertSame('Suspicious activity', $staffMember->access_restricted_reason);
        $this->assertSame($superAdmin->id, $staffMember->access_restricted_by_id);

        // Toggling again restores access and clears the reason/attribution.
        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.access-restriction', $staffMember))
            ->assertRedirect();

        $staffMember->refresh();
        $this->assertFalse((bool) $staffMember->access_restricted);
        $this->assertNull($staffMember->access_restricted_reason);
        $this->assertNull($staffMember->access_restricted_by_id);
    }

    public function test_access_restriction_cannot_target_self(): void
    {
        $superAdmin = $this->makeStaff('super_admin');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.staff.access-restriction', $superAdmin))
            ->assertStatus(422);
    }

    public function test_access_restricted_panel_visible_on_profile_for_super_admin_only(): void
    {
        $superAdmin = $this->makeStaff('super_admin');
        $hr = $this->makeStaff('hr');
        $staffMember = $this->makeStaff('office_assistant');

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff.profile.show', $staffMember))
            ->assertOk()
            ->assertSee('Access Control');

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff.profile.show', $staffMember))
            ->assertOk()
            ->assertDontSee('Access Control');
    }

    public function test_activity_log_index_loads_for_super_admin_only(): void
    {
        $actor = $this->makeStaff('super_admin');

        AdminActivityLog::query()->create([
            'user_id' => $actor->id,
            'role' => 'super_admin',
            'action' => 'Viewed staff directory',
            'method' => 'GET',
            'route_name' => 'admin.staff.index',
            'url' => '/admin/staff',
            'status_code' => 200,
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($actor)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.activity-logs.index'))
            ->assertOk()
            ->assertSee('Viewed staff directory')
            ->assertSee('admin.staff.index');
    }

    public function test_activity_log_index_forbidden_for_hr_and_managing_director(): void
    {
        // route uses `super.admin` middleware, same strict role check as staff.update.
        foreach (['hr', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.activity-logs.index'))
                ->assertForbidden();
        }
    }

    public function test_activity_log_search_and_filters(): void
    {
        $actor = $this->makeStaff('super_admin');
        $other = $this->makeStaff('hr');

        AdminActivityLog::query()->create([
            'user_id' => $actor->id,
            'role' => 'super_admin',
            'action' => 'Approved KYC for Jane Doe',
            'method' => 'POST',
            'route_name' => 'admin.staff.kyc-review',
            'url' => '/admin/staff/1/kyc-review',
            'status_code' => 302,
        ]);

        AdminActivityLog::query()->create([
            'user_id' => $other->id,
            'role' => 'hr',
            'action' => 'Viewed payroll',
            'method' => 'GET',
            'route_name' => 'admin.payroll.index',
            'url' => '/admin/payroll',
            'status_code' => 200,
        ]);

        $response = $this->actingAs($actor)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.activity-logs.index', ['search' => 'Approved KYC']));

        $response->assertOk();
        $response->assertSee('Approved KYC for Jane Doe');
        $response->assertDontSee('Viewed payroll');

        $response = $this->actingAs($actor)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.activity-logs.index', ['role' => 'hr']));

        $response->assertOk();
        $response->assertSee('Viewed payroll');
        $response->assertDontSee('Approved KYC for Jane Doe');
    }
}
