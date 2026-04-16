<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWorkflowAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase_one_cannot_advance_until_payment_is_settled(): void
    {
        $designer = $this->staff('designer');
        $order = $this->order([
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
        ]);

        $this->actingAs($designer)
            ->put(route('admin.orders.update', $order), [
                'status' => 'Design / Artwork Preparation',
            ])
            ->assertSessionHasErrors(['status']);

        $this->assertSame('Analyzing Job Brief', $order->fresh()->status);
    }

    public function test_unprivileged_staff_cannot_spoof_payment_status_to_leave_phase_one(): void
    {
        $designer = $this->staff('designer');
        $order = $this->order([
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Issued',
        ]);

        $this->actingAs($designer)
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
        $logistics = $this->staff('logistics');
        $order = $this->order([
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Settled (70%)',
        ]);

        $this->actingAs($logistics)
            ->put(route('admin.orders.update', $order), [
                'status' => 'Design / Artwork Preparation',
            ])
            ->assertSessionHasErrors(['status']);

        $this->assertSame('Analyzing Job Brief', $order->fresh()->status);
    }

    public function test_staff_only_sees_role_related_phase_information(): void
    {
        $logistics = $this->staff('logistics');
        $order = $this->order([
            'status' => 'Analyzing Job Brief',
            'payment_status' => 'Invoice Settled (70%)',
        ]);

        $this->actingAs($logistics)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSeeText('Delivery In Progress')
            ->assertDontSeeText('Design / Artwork Preparation')
            ->assertDontSeeText('2 — Design');
    }

    private function staff(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
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
