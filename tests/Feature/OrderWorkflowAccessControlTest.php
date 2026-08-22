<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWorkflowAccessControlTest extends TestCase
{
    use RefreshDatabase;

    // Note: 'designer' and 'logistics' role slugs no longer exist in
    // config/printbuka_admin.php (roles were consolidated). 'personal_assistant'
    // (has design.update, no workflow.approve/invoices.manage) and
    // 'customer_service' (has orders.intake + delivery.update, no
    // design.update/production.update/qc.update/workflow.approve) are the
    // current-config equivalents that preserve this test's intent.

    public function test_phase_one_cannot_advance_until_payment_is_settled(): void
    {
        $designer = $this->staff('personal_assistant');
        $order = $this->order([
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
        ]);

        $this->actingAs($designer)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.orders.update', $order), [
                'status' => 'Design / Artwork Preparation',
            ])
            ->assertSessionHasErrors(['status']);

        $this->assertSame('Analyzing Job Brief', $order->fresh()->status);
    }

    public function test_unprivileged_staff_cannot_spoof_payment_status_to_leave_phase_one(): void
    {
        $designer = $this->staff('personal_assistant');
        $order = $this->order([
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
        ]);

        $this->actingAs($designer)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.orders.update', $order), [
                'status' => 'Design / Artwork Preparation',
                'payment_status' => 'Invoice Settled (70%)',
            ])
            ->assertSessionHasErrors(['status']);

        $fresh = $order->fresh();
        $this->assertSame('Analyzing Job Brief', $fresh->status);
        $this->assertSame('Invoice Issued', $fresh->payment_status);
    }

    public function test_staff_cannot_move_job_to_phase_outside_role_privilege(): void
    {
        $logistics = $this->staff('customer_service');
        $order = $this->order([
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Settled (70%)',
        ]);

        $this->actingAs($logistics)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.orders.update', $order), [
                'status' => 'Design / Artwork Preparation',
            ])
            ->assertSessionHasErrors(['status']);

        $this->assertSame('Analyzing Job Brief', $order->fresh()->status);
    }

    public function test_staff_only_sees_role_related_phase_information(): void
    {
        $logistics = $this->staff('customer_service');
        // Status is set to a phase whose permission (qc.update) customer_service
        // does not hold, so the "move job forward" widget (which is keyed off
        // the CURRENT phase's permission, not the target phase's) does not
        // render and leak the next phase's name. This isolates the assertion
        // to the phase-card visibility filtering (visibleWorkflowPhasesForUser).
        $order = $this->order([
            'status' => 'Quality Check & Packaging',
            'payment_status' => 'Invoice Settled (70%)',
        ]);

        $this->actingAs($logistics)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSeeText('Delivery In Progress')
            ->assertDontSeeText('Design / Artwork Preparation')
            ->assertDontSeeText('2 — Design');
    }

    private function staff(string $role): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        if (! in_array($role, ['super_admin', 'managing_director', 'hr'], true)) {
            StaffProfile::query()->create([
                'user_id' => $user->id,
                'kyc_status' => 'approved',
            ]);
        }

        return $user;
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function order(array $overrides = []): Order
    {
        return Order::query()->create([
            'service_type' => 'print',
            'channel' => 'Manual',
            'quantity' => 20,
            'unit_price' => 500,
            'total_price' => 10000,
            'customer_name' => 'Client Example',
            'customer_email' => 'client@example.com',
            'customer_phone' => '08022223333',
            'status' => 'Analyzing Job Brief',
            'job_order_number' => 'JOB-20260416-ABC123',
            'payment_status' => 'Invoice Issued',
            ...$overrides,
        ]);
    }
}
