<?php

namespace Tests\Feature;

use App\Livewire\Admin\OrdersTable;
use App\Models\Order;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderPriorityFieldTest extends TestCase
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

    private function makeOrder(string $priority = '🟡 Normal'): Order
    {
        $customer = User::factory()->create(['role' => 'customer']);

        return Order::query()->create([
            'user_id' => $customer->id,
            'job_order_number' => 'PB-2026-PRIO'.random_int(1000, 9999),
            'customer_name' => 'Priority Test',
            'customer_email' => 'priority@example.com',
            'customer_phone' => '08000000000',
            'job_type' => 'Business Cards',
            'quantity' => 100,
            'unit_price' => 500,
            'total_price' => 50000,
            'channel' => 'Manual',
            'status' => 'Quote Requested',
            'payment_status' => 'Awaiting Invoice',
            'priority' => $priority,
            'service_type' => 'catalog',
        ]);
    }

    public function test_batch_setting_priority_uses_the_canonical_config_values(): void
    {
        $admin = $this->makeStaff('super_admin');
        $order = $this->makeOrder('🟡 Normal');

        Livewire::actingAs($admin)
            ->test(OrdersTable::class)
            ->set('selected', [$order->id])
            ->set('batchAction', 'priority_urgent')
            ->call('applyBatchAction');

        $this->assertSame(config('printbuka_admin.priorities.0'), $order->fresh()->priority);
        $this->assertSame('🔴 Urgent', $order->fresh()->priority);
    }

    public function test_batch_setting_priority_normal_uses_the_canonical_config_value(): void
    {
        $admin = $this->makeStaff('super_admin');
        $order = $this->makeOrder('🔴 Urgent');

        Livewire::actingAs($admin)
            ->test(OrdersTable::class)
            ->set('selected', [$order->id])
            ->set('batchAction', 'priority_normal')
            ->call('applyBatchAction');

        $this->assertSame('🟡 Normal', $order->fresh()->priority);
    }

    public function test_priority_badge_helpers_handle_canonical_values(): void
    {
        $urgent = $this->makeOrder('🔴 Urgent');
        $normal = $this->makeOrder('🟡 Normal');
        $low = $this->makeOrder('🟢 Low');

        $this->assertSame('urgent', $urgent->priorityLevel());
        $this->assertSame('normal', $normal->priorityLevel());
        $this->assertSame('low', $low->priorityLevel());

        $this->assertSame('🔴 Urgent', $urgent->priorityLabel());
        $this->assertSame('🟡 Normal', $normal->priorityLabel());
        $this->assertSame('🟢 Low', $low->priorityLabel());
    }

    public function test_priority_badge_helpers_are_resilient_to_off_scheme_or_corrupted_values(): void
    {
        // The pre-fix batch action bug and any historical mojibake corruption
        // both leave the ASCII keyword intact — the helpers must still classify
        // these correctly by matching on the word, not the (possibly wrong or
        // corrupted) emoji bytes.
        $wrongEmoji = $this->makeOrder('🟥 Urgent');
        $mojibake = $this->makeOrder("\xc3\xb0\xc5\xb8\xc5\xb8\xc2\xa1 Normal");
        $empty = $this->makeOrder('');

        $this->assertSame('urgent', $wrongEmoji->priorityLevel());
        $this->assertSame('normal', $mojibake->priorityLevel());
        $this->assertSame('none', $empty->priorityLevel());
        $this->assertSame('—', $empty->priorityLabel());
    }
}
