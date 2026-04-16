<?php

namespace Tests\Feature;

use App\Mail\InvoicePaidReceiptMail;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoicePaidReceiptNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_receives_receipt_email_when_invoice_marked_paid_by_admin(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $order = Order::query()->create([
            'service_type' => 'print',
            'quantity' => 10,
            'unit_price' => 1000,
            'total_price' => 10000,
            'customer_name' => 'Ada Client',
            'customer_email' => 'ada@example.com',
            'customer_phone' => '08012345678',
            'status' => 'Analyzing Job Brief',
            'job_order_number' => 'JOB-20260416-ABC123',
            'payment_status' => 'Pending Payment',
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-20260416-ABC123',
            'subtotal' => 10000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 10000,
            'status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.invoices.update', $invoice), [
                'order_id' => $order->id,
                'invoice_number' => $invoice->invoice_number,
                'subtotal' => 10000,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 10000,
                'status' => 'paid',
                'issued_at' => now()->format('Y-m-d H:i:s'),
                'due_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('admin.invoices.index'));

        $invoice->refresh();
        $order->refresh();

        $this->assertSame('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
        $this->assertSame('Invoice Settled (100%)', $order->payment_status);
        $this->assertSame('10000.00', (string) $order->amount_paid);

        Mail::assertSent(InvoicePaidReceiptMail::class, function (InvoicePaidReceiptMail $mail): bool {
            return $mail->hasTo('ada@example.com');
        });
    }

    public function test_express_order_eta_is_rebased_to_48_hours_from_payment_time(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $order = Order::query()->create([
            'service_type' => 'print',
            'quantity' => 5,
            'unit_price' => 2000,
            'total_price' => 10000,
            'customer_name' => 'Express Client',
            'customer_email' => 'express@example.com',
            'customer_phone' => '08012345678',
            'status' => 'Analyzing Job Brief',
            'job_order_number' => 'JOB-20260416-EXP123',
            'payment_status' => 'Pending Payment',
            'is_express' => true,
            'estimated_delivery_at' => now()->addDays(5),
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-20260416-EXP123',
            'subtotal' => 10000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 10000,
            'status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.invoices.update', $invoice), [
                'order_id' => $order->id,
                'invoice_number' => $invoice->invoice_number,
                'subtotal' => 10000,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 10000,
                'status' => 'paid',
                'issued_at' => now()->format('Y-m-d H:i:s'),
                'due_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('admin.invoices.index'));

        $invoice->refresh();
        $order->refresh();

        $this->assertNotNull($invoice->paid_at);
        $this->assertNotNull($order->estimated_delivery_at);
        $this->assertSame(
            $invoice->paid_at->copy()->addHours(48)->timestamp,
            $order->estimated_delivery_at?->timestamp
        );
    }

    public function test_customer_service_can_mark_invoice_as_disputed(): void
    {
        Mail::fake();

        $customerService = User::factory()->create([
            'role' => 'customer_service',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $order = Order::query()->create([
            'service_type' => 'print',
            'quantity' => 10,
            'unit_price' => 1000,
            'total_price' => 10000,
            'customer_name' => 'Dispute Client',
            'customer_email' => 'dispute@example.com',
            'customer_phone' => '08012345678',
            'status' => 'Analyzing Job Brief',
            'job_order_number' => 'JOB-20260416-DSP123',
            'payment_status' => 'Pending Payment',
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-20260416-DSP123',
            'subtotal' => 10000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 10000,
            'status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);

        $this->actingAs($customerService)
            ->put(route('admin.invoices.update', $invoice), [
                'order_id' => $order->id,
                'invoice_number' => $invoice->invoice_number,
                'subtotal' => 10000,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 10000,
                'status' => 'disputed',
                'issued_at' => now()->format('Y-m-d H:i:s'),
                'due_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('admin.invoices.index'));

        $invoice->refresh();
        $this->assertSame('disputed', $invoice->status);
    }
}
