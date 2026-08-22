<?php

namespace Tests\Feature;

use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use App\Models\SalaryStructure;
use App\Models\StaffEvaluation;
use App\Models\StaffProfile;
use App\Models\StaffQuery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffModuleAuditTest extends TestCase
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

    public function test_all_admin_roles_can_view_payroll_queries_and_evaluations_index(): void
    {
        foreach (['super_admin', 'managing_director', 'hr'] as $role) {
            $user = $this->makeStaff($role);

            $this->actingAs($user)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.payroll.index'))
                ->assertOk();

            $this->actingAs($user)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.staff-queries.index'))
                ->assertOk();

            $this->actingAs($user)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.evaluations.index'))
                ->assertOk();
        }
    }

    public function test_previous_payroll_runs_are_visible_to_hr_and_md(): void
    {
        $creator = $this->makeStaff('super_admin');
        $staffMember = $this->makeStaff('office_assistant');

        $run = PayrollRun::query()->create([
            'payroll_month' => 5,
            'payroll_year' => 2026,
            'status' => 'paid',
            'created_by_id' => $creator->id,
        ]);

        PayrollEntry::query()->create([
            'payroll_run_id' => $run->id,
            'staff_id' => $staffMember->id,
            'basic_salary' => 100000,
            'gross_salary' => 100000,
            'net_salary' => 90000,
        ]);

        foreach (['hr', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);

            $response = $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.payroll.index'));

            $response->assertOk();
            $response->assertSee('May 2026');

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.payroll.run', $run))
                ->assertOk()
                ->assertSee($staffMember->displayName());
        }
    }

    public function test_previous_staff_queries_are_visible_to_hr_and_md(): void
    {
        $issuer = $this->makeStaff('super_admin');
        $staffMember = $this->makeStaff('office_assistant');

        $query = StaffQuery::query()->create([
            'staff_id' => $staffMember->id,
            'issued_by_id' => $issuer->id,
            'query_number' => 'QRY-2026-0001',
            'query_date' => now()->subMonths(2),
            'query_type' => 'written_warning',
            'subject' => 'Late submission',
            'description' => 'Repeated late submissions of job cards.',
            'status' => 'closed',
        ]);

        foreach (['hr', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.staff-queries.index'))
                ->assertOk()
                ->assertSee('QRY-2026-0001');

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.staff-queries.show', $query))
                ->assertOk();
        }
    }

    public function test_previous_evaluations_are_visible_to_hr_and_md(): void
    {
        $evaluator = $this->makeStaff('super_admin');
        $staffMember = $this->makeStaff('office_assistant');

        $evaluation = StaffEvaluation::query()->create([
            'staff_id' => $staffMember->id,
            'evaluated_by_id' => $evaluator->id,
            'period_month' => 3,
            'period_year' => 2026,
            'overall_rating' => 4,
            'status' => 'submitted',
        ]);

        foreach (['hr', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.evaluations.index'))
                ->assertOk()
                ->assertSee('March 2026');

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.evaluations.show', $evaluation))
                ->assertOk();
        }
    }

    public function test_salary_structure_page_loads(): void
    {
        $user = $this->makeStaff('hr');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.payroll.salary-structures'))
            ->assertOk();
    }

    public function test_create_payroll_run_page_loads(): void
    {
        $user = $this->makeStaff('super_admin');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.payroll.create-run'))
            ->assertOk();
    }

    public function test_create_staff_query_page_loads(): void
    {
        $user = $this->makeStaff('hr');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff-queries.create'))
            ->assertOk();
    }

    public function test_create_evaluation_page_loads(): void
    {
        $user = $this->makeStaff('managing_director');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.evaluations.create'))
            ->assertOk();
    }

    public function test_full_payroll_run_lifecycle(): void
    {
        $admin = $this->makeStaff('super_admin');
        $staffMember = $this->makeStaff('office_assistant');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.payroll.salary-store'), [
                'staff_id' => $staffMember->id,
                'effective_date' => now()->subMonth()->format('Y-m-d'),
                'basic_salary' => 150000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('salary_structures', ['staff_id' => $staffMember->id, 'is_active' => 1]);

        $response = $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.payroll.store-run'), [
                'payroll_month' => now()->month,
                'payroll_year' => now()->year,
            ]);
        $response->assertRedirect();

        $run = PayrollRun::query()->firstOrFail();
        $this->assertSame(1, $run->entries()->count());

        $entry = $run->entries()->firstOrFail();

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.payroll.update-entry', $entry), [
                'basic_salary' => 150000,
                'housing_allowance' => 10000,
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.payroll.finalize', $run))
            ->assertRedirect();

        $run->refresh();
        $this->assertSame('finalized', $run->status);

        foreach (['hr', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);
            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.payroll.run', $run))
                ->assertOk();

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.payroll.run.download', $run))
                ->assertOk();

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.payroll.payslip.download', $entry->fresh()))
                ->assertOk();
        }
    }

    public function test_full_staff_query_lifecycle(): void
    {
        $issuer = $this->makeStaff('hr');
        $staffMember = $this->makeStaff('office_assistant');

        $response = $this->actingAs($issuer)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff-queries.store'), [
                'staff_id' => $staffMember->id,
                'query_date' => now()->format('Y-m-d'),
                'query_type' => 'written_warning',
                'subject' => 'Late submission',
                'description' => 'Repeated late submission of job cards.',
            ]);
        $response->assertRedirect();

        $query = StaffQuery::query()->firstOrFail();

        $this->actingAs($staffMember)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff-queries.respond', $query), [
                'staff_response' => 'It will not happen again.',
            ])
            ->assertRedirect();

        $this->actingAs($issuer)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.staff-queries.close', $query), [
                'resolution_notes' => 'Resolved after discussion.',
            ])
            ->assertRedirect();

        $query->refresh();
        $this->assertSame('closed', $query->status);

        foreach (['super_admin', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);
            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.staff-queries.show', $query))
                ->assertOk();
        }
    }

    public function test_full_evaluation_lifecycle(): void
    {
        $evaluator = $this->makeStaff('hr');
        $staffMember = $this->makeStaff('office_assistant');

        $response = $this->actingAs($evaluator)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.evaluations.store'), [
                'staff_id' => $staffMember->id,
                'period_month' => now()->month,
                'period_year' => now()->year,
                'overall_rating' => 4,
                'punctuality_rating' => 5,
            ]);
        $response->assertRedirect();

        $evaluation = StaffEvaluation::query()->firstOrFail();

        $this->actingAs($staffMember)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.evaluations.acknowledge', $evaluation))
            ->assertRedirect();

        $evaluation->refresh();
        $this->assertTrue((bool) $evaluation->staff_acknowledged);

        foreach (['super_admin', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);
            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.evaluations.show', $evaluation))
                ->assertOk();
        }
    }

    public function test_staff_directory_and_profile_pages_load_for_all_admin_roles(): void
    {
        $staffMember = $this->makeStaff('office_assistant');

        foreach (['super_admin', 'hr', 'managing_director'] as $role) {
            $viewer = $this->makeStaff($role);

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.staff.index'))
                ->assertOk();

            $this->actingAs($viewer)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.staff.profile.show', $staffMember))
                ->assertOk();
        }
    }

    public function test_training_applications_index_loads(): void
    {
        $user = $this->makeStaff('hr');

        $this->actingAs($user)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.training.index'))
            ->assertOk();
    }
}
