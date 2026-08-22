<?php

namespace Tests\Feature;

use App\Mail\FinanceReportMail;
use App\Mail\InvoicePaidReceiptMail;
use App\Mail\PayrollRunMail;
use App\Mail\PayslipMail;
use App\Mail\ShopOrderConfirmationMail;
use App\Mail\ShopOrderFailedMail;
use App\Mail\ShopOrderStatusUpdateMail;
use App\Mail\UnpaidInvoiceReminderMail;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EmailCustomizerWiringBatchTest extends TestCase
{
    use RefreshDatabase;

    private function assertBaselineThenOverride(string $key, callable $makeMail, string $baselineNeedle): void
    {
        // Baseline: no EmailTemplate row present — output must render successfully and
        // contain the pre-existing content unchanged.
        $mail = $makeMail();
        $rendered = $mail->render();
        $this->assertStringContainsString($baselineNeedle, $rendered);
        $this->assertStringNotContainsString('CUSTOM-INTRO-MARKER', $rendered);
        $this->assertStringNotContainsString('CUSTOM-OUTRO-MARKER', $rendered);

        // Override: create an EmailTemplate row for this key, re-render, confirm custom text appears.
        EmailTemplate::query()->create([
            'key' => $key,
            'name' => 'Test override for '.$key,
            'intro_blocks' => [
                ['id' => '1', 'type' => 'paragraph', 'text' => 'CUSTOM-INTRO-MARKER'],
            ],
            'outro_blocks' => [
                ['id' => '2', 'type' => 'paragraph', 'text' => 'CUSTOM-OUTRO-MARKER'],
            ],
        ]);

        $mail2 = $makeMail();
        $rendered2 = $mail2->render();
        $this->assertStringContainsString('CUSTOM-INTRO-MARKER', $rendered2);
        $this->assertStringContainsString('CUSTOM-OUTRO-MARKER', $rendered2);
        $this->assertStringContainsString($baselineNeedle, $rendered2);
    }

    private function makeOrderAndInvoice(string $suffix): array
    {
        $order = Order::query()->create([
            'service_type' => 'print',
            'quantity' => 10,
            'unit_price' => 1000,
            'total_price' => 10000,
            'customer_name' => 'Ada Client '.$suffix,
            'customer_email' => 'ada'.$suffix.'@example.com',
            'customer_phone' => '08012345678',
            'status' => 'Analyzing Job Brief',
            'job_order_number' => 'JOB-20260416-'.$suffix,
            'payment_status' => 'Pending Payment',
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-20260416-'.$suffix,
            'subtotal' => 10000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 10000,
            'status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);

        return [$order, $invoice];
    }

    public function test_finance_report_mail_renders_baseline_and_override(): void
    {
        $this->assertBaselineThenOverride(
            'finance.report',
            fn () => new FinanceReportMail(
                new Collection([]),
                50000.0,
                20000.0,
                30000.0,
                'monthly',
                null,
                null,
                'Jane Admin',
                'April 2026'
            ),
            'April 2026 Report'
        );
    }

    public function test_invoice_paid_receipt_mail_renders_baseline_and_override(): void
    {
        [, $invoice] = $this->makeOrderAndInvoice('PDR001');

        $this->assertBaselineThenOverride(
            'invoice.paid_receipt',
            fn () => new InvoicePaidReceiptMail($invoice),
            'Payment confirmed'
        );
    }

    public function test_payroll_run_mail_renders_baseline_and_override(): void
    {
        $creator = User::factory()->create(['role' => 'super_admin']);
        $staffMember = User::factory()->create(['role' => 'office_assistant']);

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

        $this->assertBaselineThenOverride(
            'payroll.run',
            fn () => new PayrollRunMail($run->fresh(), 'Jane Admin'),
            'Printbuka Payroll Summary'
        );
    }

    public function test_payslip_mail_renders_baseline_and_override(): void
    {
        $creator = User::factory()->create(['role' => 'super_admin']);
        $staffMember = User::factory()->create(['role' => 'office_assistant']);

        $run = PayrollRun::query()->create([
            'payroll_month' => 5,
            'payroll_year' => 2026,
            'status' => 'paid',
            'created_by_id' => $creator->id,
        ]);

        $entry = PayrollEntry::query()->create([
            'payroll_run_id' => $run->id,
            'staff_id' => $staffMember->id,
            'basic_salary' => 100000,
            'gross_salary' => 100000,
            'net_salary' => 90000,
        ]);

        $this->assertBaselineThenOverride(
            'payroll.payslip',
            fn () => new PayslipMail($entry->fresh()),
            'Printbuka Payslip'
        );
    }

    public function test_unpaid_invoice_reminder_mail_renders_baseline_and_override(): void
    {
        [, $invoice] = $this->makeOrderAndInvoice('UNP001');

        $this->assertBaselineThenOverride(
            'invoice.unpaid_reminder',
            fn () => new UnpaidInvoiceReminderMail($invoice),
            'Payment reminder'
        );
    }

    private function makeShopOrder(string $suffix): ShopOrder
    {
        $order = ShopOrder::query()->create([
            'reference' => 'SHOP-'.$suffix,
            'customer_name' => 'Shop Customer '.$suffix,
            'customer_email' => 'shop'.$suffix.'@example.com',
            'customer_phone' => '08012345678',
            'shipping_name' => 'Shop Customer '.$suffix,
            'shipping_address' => '1 Test Street',
            'shipping_city' => 'Lagos',
            'shipping_state' => 'Lagos',
            'subtotal' => 5000,
            'total' => 5000,
            'payment_status' => 'paid',
            'fulfillment_status' => 'order_received',
        ]);

        ShopOrderItem::query()->create([
            'shop_order_id' => $order->id,
            'product_name' => 'Test Product',
            'unit_price' => 5000,
            'quantity' => 1,
            'line_total' => 5000,
        ]);

        return $order->fresh();
    }

    public function test_shop_order_confirmation_mail_renders_baseline_and_override(): void
    {
        $order = $this->makeShopOrder('CNF001');

        $this->assertBaselineThenOverride(
            'shop.order_confirmation',
            fn () => new ShopOrderConfirmationMail($order),
            'Payment Successful!'
        );
    }

    public function test_shop_order_failed_mail_renders_baseline_and_override(): void
    {
        $order = $this->makeShopOrder('FLD001');

        $this->assertBaselineThenOverride(
            'shop.order_failed',
            fn () => new ShopOrderFailedMail($order),
            'Payment Unsuccessful'
        );
    }

    public function test_shop_order_status_update_mail_renders_baseline_and_override(): void
    {
        $order = $this->makeShopOrder('STU001');

        $this->assertBaselineThenOverride(
            'shop.order_status_update',
            fn () => new ShopOrderStatusUpdateMail($order, 'dispatched'),
            'Order Dispatched'
        );
    }
}
