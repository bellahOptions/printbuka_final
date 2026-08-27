<?php

namespace Tests\Feature;

use App\Models\FinanceEntry;
use App\Models\Order;
use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * dompdf silently substitutes "?" for any glyph missing from the currently
 * active font. Every PDF template in this app pins the Naira symbol to
 * DejaVu Sans (which has the glyph) via a <span class="naira"> wrapper,
 * since several of the "pretty" fonts used elsewhere on the page don't.
 * These tests catch a Naira symbol added without that wrapper before it
 * ships as a "?" in production.
 */
class PdfCurrencySymbolRenderingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Strips <style> blocks before counting symbol occurrences — the
     * explanatory CSS comments ("/* ₦ pinned to DejaVu Sans... *\/") contain
     * a literal ₦ that isn't rendered body content and would otherwise skew
     * the count.
     */
    private function bodyOnly(string $html): string
    {
        return (string) preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html);
    }

    public function test_payroll_run_pdf_pins_every_naira_symbol_to_a_glyph_complete_font(): void
    {
        $staff = User::factory()->create(['role' => 'office_assistant']);

        $creator = User::factory()->create(['role' => 'hr']);

        $run = PayrollRun::query()->create([
            'payroll_month' => 8,
            'payroll_year' => 2026,
            'status' => 'finalized',
            'created_by_id' => $creator->id,
        ]);

        PayrollEntry::query()->create([
            'payroll_run_id' => $run->id,
            'staff_id' => $staff->id,
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

        $entries = $run->entries()->with('staff')->get();

        $html = view('admin.payroll.run-pdf', [
            'run' => $run->load('createdBy', 'finalizedBy'),
            'entries' => $entries,
            'totalGross' => $entries->sum('gross_salary'),
            'totalDeductions' => $entries->sum('total_deductions'),
            'totalNet' => $entries->sum('net_salary'),
        ])->render();

        $body = $this->bodyOnly($html);
        $nairaEntities = substr_count($body, '&#8358;');
        $wrappedSpans = substr_count($body, '<span class="naira">&#8358;</span>');

        $this->assertGreaterThan(0, $nairaEntities);
        $this->assertSame($nairaEntities, $wrappedSpans);
    }

    public function test_expense_log_pdf_pins_every_naira_symbol_to_a_glyph_complete_font(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'job_order_number' => 'PB-2026-NAIRA1',
            'customer_name' => 'Naira Test',
            'customer_email' => 'naira@example.com',
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

        FinanceEntry::query()->create([
            'order_id' => $order->id,
            'type' => 'expense',
            'entry_type' => 'manual',
            'category' => 'Materials',
            'description' => 'Card stock',
            'entry_date' => now(),
            'amount' => 12000,
        ]);

        $expenseEntries = FinanceEntry::query()->where('order_id', $order->id)->get();

        $html = view('admin.orders.expense-log-pdf', [
            'order' => $order,
            'expenseEntries' => $expenseEntries,
        ])->render();

        $body = $this->bodyOnly($html);
        $rawNaira = substr_count($body, '₦');
        $wrappedSpans = substr_count($body, '<span class="naira">₦</span>');

        $this->assertGreaterThan(0, $rawNaira);
        $this->assertSame($rawNaira, $wrappedSpans);
    }
}
