<?php

namespace Tests\Feature;

use App\Models\FinanceEntry;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInvoiceDeletionTest extends TestCase
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

    public function test_deleting_an_invoice_deletes_its_order_and_finance_entries_even_when_paid(): void
    {
        $manager = $this->makeStaff('operations_manager');
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'job_order_number' => 'PB-2026-DEL01',
            'customer_name' => 'Delete Me',
            'customer_email' => 'delete@example.com',
            'customer_phone' => '08000000000',
            'job_type' => 'Business Cards',
            'quantity' => 100,
            'unit_price' => 500,
            'total_price' => 50000,
            'channel' => 'Manual',
            'status' => 'Quote Requested',
            'payment_status' => 'Awaiting Invoice',
            'priority' => '🟡 Normal',
            'service_type' => 'catalog',
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-DEL-0001',
            'subtotal' => 50000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 50000,
            'status' => 'paid',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
            'paid_at' => now(),
        ]);

        $financeEntry = FinanceEntry::query()->create([
            'order_id' => $order->id,
            'type' => 'income',
            'entry_type' => 'auto_income',
            'category' => 'Invoice Payment',
            'description' => 'Full Payment — Invoice INV-DEL-0001',
            'entry_date' => now(),
            'payee' => $order->customer_name,
            'amount' => 50000,
            'payment_method' => 'Bank Transfer',
        ]);

        $this->actingAs($manager)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.invoices.destroy', $invoice))
            ->assertRedirect();

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('finance_entries', ['id' => $financeEntry->id]);
    }
}
