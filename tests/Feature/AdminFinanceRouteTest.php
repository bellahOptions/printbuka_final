<?php

namespace Tests\Feature;

use App\Models\FinanceEntry;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFinanceRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_finance_index_and_create_pages(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.finance.index'))
            ->assertOk();

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.finance.create'))
            ->assertOk()
            ->assertViewIs('admin.finance.form');
    }

    public function test_customer_service_can_view_and_create_finance_entries(): void
    {
        $customerService = User::factory()->create([
            'role' => 'customer_service',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        StaffProfile::query()->create([
            'user_id' => $customerService->id,
            'kyc_status' => 'approved',
        ]);

        $this->actingAs($customerService)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.finance.index'))
            ->assertOk();

        $this->actingAs($customerService)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.finance.create'))
            ->assertOk()
            ->assertViewIs('admin.finance.form');
    }

    public function test_creating_a_finance_entry_logs_the_staff_member_who_recorded_it(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.finance.store'), [
                'entry_date' => now()->format('Y-m-d'),
                'type' => 'expense',
                'category' => 'Supplies',
                'description' => 'Printer ink',
                'amount' => 5000,
            ])
            ->assertRedirect(route('admin.finance.index'));

        $entry = FinanceEntry::query()->firstOrFail();

        $this->assertSame($superAdmin->id, $entry->user_id);
        $this->assertNull($entry->last_edited_by);
    }

    public function test_resubmitting_the_same_finance_entry_with_the_same_idempotency_key_does_not_duplicate_it(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        $payload = [
            'entry_date' => now()->format('Y-m-d'),
            'type' => 'expense',
            'category' => 'Supplies',
            'description' => 'Printer ink',
            'amount' => 5000,
            'idempotency_key' => 'test-idem-key-001',
        ];

        // Simulates a double-click or a slow-network retry resending the
        // exact same form submission (same client-generated key) twice.
        $this->actingAs($superAdmin)->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.finance.store'), $payload)
            ->assertRedirect(route('admin.finance.index'));

        $this->actingAs($superAdmin)->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.finance.store'), $payload)
            ->assertRedirect(route('admin.finance.index'));

        $this->assertSame(1, FinanceEntry::query()->where('idempotency_key', 'test-idem-key-001')->count());
    }

    public function test_a_different_idempotency_key_creates_a_genuinely_separate_entry(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        $base = [
            'entry_date' => now()->format('Y-m-d'),
            'type' => 'expense',
            'category' => 'Supplies',
            'description' => 'Printer ink',
            'amount' => 5000,
        ];

        $this->actingAs($superAdmin)->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.finance.store'), [...$base, 'idempotency_key' => 'key-a'])
            ->assertRedirect();

        // A real second purchase of the same category/amount on the same day
        // — a fresh page load gives a fresh key, so this must NOT be blocked.
        $this->actingAs($superAdmin)->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.finance.store'), [...$base, 'idempotency_key' => 'key-b'])
            ->assertRedirect();

        $this->assertSame(2, FinanceEntry::query()->where('category', 'Supplies')->count());
    }

    public function test_finance_entry_amount_is_rounded_to_two_decimals(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.finance.store'), [
                'entry_date' => now()->format('Y-m-d'),
                'type' => 'expense',
                'category' => 'Supplies',
                'description' => 'Printer ink',
                'amount' => '5000.005',
            ])
            ->assertRedirect();

        $entry = FinanceEntry::query()->firstOrFail();
        $this->assertEqualsWithDelta(5000.01, (float) $entry->amount, 0.001);
    }

    public function test_updating_a_finance_entry_logs_the_editor_without_changing_the_original_recorder(): void
    {
        $creator = User::factory()->create(['role' => 'super_admin']);
        $editor = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        $entry = FinanceEntry::query()->create([
            'user_id' => $creator->id,
            'entry_date' => now(),
            'type' => 'expense',
            'category' => 'Supplies',
            'description' => 'Printer ink',
            'amount' => 5000,
        ]);

        $this->actingAs($editor)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.finance.update', $entry), [
                'entry_date' => now()->format('Y-m-d'),
                'type' => 'expense',
                'category' => 'Supplies',
                'description' => 'Printer ink and toner',
                'amount' => 6000,
            ])
            ->assertRedirect(route('admin.finance.index'));

        $entry->refresh();

        $this->assertSame($creator->id, $entry->user_id);
        $this->assertSame($editor->id, $entry->last_edited_by);
        $this->assertNotNull($entry->last_edited_at);
    }

    public function test_customer_service_can_update_and_delete_finance_entries(): void
    {
        $creator = User::factory()->create(['role' => 'super_admin']);
        $customerService = User::factory()->create([
            'role' => 'customer_service',
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        StaffProfile::query()->create([
            'user_id' => $customerService->id,
            'kyc_status' => 'approved',
        ]);

        $entry = FinanceEntry::query()->create([
            'user_id' => $creator->id,
            'entry_date' => now(),
            'type' => 'expense',
            'category' => 'Supplies',
            'description' => 'Printer ink',
            'amount' => 5000,
        ]);

        $this->actingAs($customerService)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.finance.update', $entry), [
                'entry_date' => now()->format('Y-m-d'),
                'type' => 'expense',
                'category' => 'Supplies',
                'description' => 'Printer ink and toner',
                'amount' => 6000,
            ])
            ->assertRedirect(route('admin.finance.index'));

        $this->assertDatabaseHas('finance_entries', ['id' => $entry->id, 'description' => 'Printer ink and toner']);

        $this->actingAs($customerService)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.finance.destroy', $entry))
            ->assertRedirect();

        $this->assertDatabaseMissing('finance_entries', ['id' => $entry->id]);
    }
}

