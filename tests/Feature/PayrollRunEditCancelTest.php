<?php

namespace Tests\Feature;

use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollRunEditCancelTest extends TestCase
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

    private function makeRun(string $status = 'draft', array $overrides = []): PayrollRun
    {
        $creator = $this->makeStaff('hr');
        $staffMember = $this->makeStaff('office_assistant');

        $run = PayrollRun::query()->create(array_merge([
            'payroll_month' => 8,
            'payroll_year' => 2026,
            'status' => $status,
            'created_by_id' => $creator->id,
        ], $overrides));

        PayrollEntry::query()->create([
            'payroll_run_id' => $run->id,
            'staff_id' => $staffMember->id,
            'basic_salary' => 150000,
            'housing_allowance' => 0,
            'transport_allowance' => 0,
            'medical_allowance' => 0,
            'other_allowances' => 0,
            'gross_salary' => 150000,
            'pension_deduction' => 0,
            'tax_deduction' => 0,
            'other_deductions' => 0,
            'total_deductions' => 0,
            'net_salary' => 150000,
        ]);

        return $run;
    }

    public function test_hr_can_edit_a_pending_payroll_run(): void
    {
        $hr = $this->makeStaff('hr');
        $run = $this->makeRun();

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.payroll.edit-run', $run))
            ->assertOk();

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.payroll.update-run', $run), [
                'payroll_month' => 9,
                'payroll_year' => 2026,
                'payment_date' => '2026-09-30',
                'notes' => 'Corrected the period.',
            ])
            ->assertRedirect(route('admin.payroll.run', $run));

        $run->refresh();
        $this->assertSame(9, $run->payroll_month);
        $this->assertSame('2026-09-30', $run->payment_date->toDateString());
        $this->assertSame('Corrected the period.', $run->notes);
    }

    public function test_editing_a_payroll_run_rejects_a_duplicate_period(): void
    {
        $hr = $this->makeStaff('hr');
        $this->makeRun(overrides: ['payroll_month' => 9, 'payroll_year' => 2026]);
        $runToEdit = $this->makeRun(overrides: ['payroll_month' => 8, 'payroll_year' => 2026]);

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.payroll.update-run', $runToEdit), [
                'payroll_month' => 9,
                'payroll_year' => 2026,
            ])
            ->assertSessionHasErrors('payroll_month');

        $this->assertSame(8, $runToEdit->fresh()->payroll_month);
    }

    public function test_a_finalized_payroll_run_cannot_be_edited(): void
    {
        $hr = $this->makeStaff('hr');
        $run = $this->makeRun(status: 'finalized');

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.payroll.edit-run', $run))
            ->assertStatus(422);

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.payroll.update-run', $run), [
                'payroll_month' => 9,
                'payroll_year' => 2026,
            ])
            ->assertStatus(422);
    }

    public function test_hr_can_cancel_a_pending_payroll_run(): void
    {
        $hr = $this->makeStaff('hr');
        $run = $this->makeRun();
        $entryId = $run->entries()->firstOrFail()->id;

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.payroll.destroy-run', $run))
            ->assertRedirect(route('admin.payroll.index'));

        $this->assertDatabaseMissing('payroll_runs', ['id' => $run->id]);
        $this->assertDatabaseMissing('payroll_entries', ['id' => $entryId]);
    }

    public function test_a_finalized_payroll_run_cannot_be_cancelled(): void
    {
        $hr = $this->makeStaff('hr');
        $run = $this->makeRun(status: 'finalized');

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.payroll.destroy-run', $run))
            ->assertStatus(422);

        $this->assertDatabaseHas('payroll_runs', ['id' => $run->id]);
    }

    public function test_staff_without_payroll_manage_permission_cannot_edit_or_cancel(): void
    {
        $viewer = $this->makeStaff('operations_manager');
        $run = $this->makeRun();

        $this->actingAs($viewer)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.payroll.edit-run', $run))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.payroll.destroy-run', $run))
            ->assertForbidden();

        $this->assertDatabaseHas('payroll_runs', ['id' => $run->id]);
    }
}
