<?php

namespace Tests\Feature;

use App\Models\FinanceEntry;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FinanceInvoiceReconciliationTest extends TestCase
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

    private function makeInvoice(float $total): Invoice
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'job_order_number' => 'PB-2026-RECON',
            'customer_name' => 'Recon Test',
            'customer_email' => 'recon@example.com',
            'customer_phone' => '08000000000',
            'job_type' => 'Business Cards',
            'quantity' => 100,
            'unit_price' => 500,
            'total_price' => $total,
            'channel' => 'Manual',
            'status' => 'Quote Requested',
            'payment_status' => 'Awaiting Invoice',
            'priority' => '🟡 Normal',
            'service_type' => 'catalog',
        ]);

        return Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-RECON-0001',
            'subtotal' => $total,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $total,
            'status' => 'pending',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);
    }

    public function test_invoice_paid_in_two_instalments_is_not_double_counted_in_finance_income(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $invoice = $this->makeInvoice(100000);

        // First instalment: 50%.
        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.invoices.record-payment', $invoice), [
                'amount' => 50000,
                'payment_method' => 'Bank Transfer',
                'paid_at' => now()->toDateString(),
            ])
            ->assertRedirect();

        // Second instalment brings it to 100% — this is the path that previously
        // double/triple-counted income (per-instalment entry + a redundant
        // full-total sync entry from InvoiceLifecycleService).
        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.invoices.record-payment', $invoice), [
                'amount' => 50000,
                'payment_method' => 'Bank Transfer',
                'paid_at' => now()->toDateString(),
            ])
            ->assertRedirect();

        $invoice->refresh();
        $this->assertSame('paid', $invoice->status);

        $totalIncomeRecorded = FinanceEntry::query()
            ->where('order_id', $invoice->order_id)
            ->where('type', 'income')
            ->sum('amount');

        // Must equal exactly what was actually paid — not double or triple it.
        $this->assertEqualsWithDelta(100000.0, (float) $totalIncomeRecorded, 0.01);
    }

    public function test_invoice_paid_in_full_without_instalments_records_income_once(): void
    {
        Mail::fake();

        $invoice = $this->makeInvoice(75000);

        app(\App\Services\InvoiceLifecycleService::class)->handleStatusChange(
            tap($invoice)->update(['status' => 'paid', 'paid_at' => now()]),
            'pending'
        );

        $totalIncomeRecorded = FinanceEntry::query()
            ->where('order_id', $invoice->order_id)
            ->where('type', 'income')
            ->sum('amount');

        $this->assertEqualsWithDelta(75000.0, (float) $totalIncomeRecorded, 0.01);
        $this->assertSame(1, FinanceEntry::query()->where('order_id', $invoice->order_id)->count());
    }

    public function test_resubmitting_the_same_payment_with_the_same_idempotency_key_does_not_duplicate_it(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $invoice = $this->makeInvoice(100000);

        $payload = [
            'amount' => 50000,
            'payment_method' => 'Bank Transfer',
            'paid_at' => now()->toDateString(),
            'idempotency_key' => 'pay-idem-key-001',
        ];

        // Simulates a double-click or a slow-network retry resending the
        // exact same payment submission (same client-generated key) twice.
        $this->actingAs($admin)->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.invoices.record-payment', $invoice), $payload)
            ->assertRedirect();

        $this->actingAs($admin)->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.invoices.record-payment', $invoice), $payload)
            ->assertRedirect();

        $this->assertSame(1, InvoicePayment::query()->where('idempotency_key', 'pay-idem-key-001')->count());

        $totalIncomeRecorded = FinanceEntry::query()
            ->where('order_id', $invoice->order_id)
            ->where('type', 'income')
            ->sum('amount');

        $this->assertEqualsWithDelta(50000.0, (float) $totalIncomeRecorded, 0.01);
    }

    public function test_a_different_idempotency_key_records_a_genuinely_separate_instalment(): void
    {
        Mail::fake();

        $admin = $this->makeStaff('super_admin');
        $invoice = $this->makeInvoice(100000);

        $this->actingAs($admin)->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.invoices.record-payment', $invoice), [
                'amount' => 50000,
                'payment_method' => 'Bank Transfer',
                'paid_at' => now()->toDateString(),
                'idempotency_key' => 'pay-key-a',
            ])
            ->assertRedirect();

        // A real second instalment — a fresh page load gives a fresh key,
        // so this must NOT be blocked.
        $this->actingAs($admin)->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.invoices.record-payment', $invoice), [
                'amount' => 50000,
                'payment_method' => 'Bank Transfer',
                'paid_at' => now()->toDateString(),
                'idempotency_key' => 'pay-key-b',
            ])
            ->assertRedirect();

        $this->assertSame(2, InvoicePayment::query()->where('invoice_id', $invoice->id)->count());
        $invoice->refresh();
        $this->assertSame('paid', $invoice->status);
    }
}
