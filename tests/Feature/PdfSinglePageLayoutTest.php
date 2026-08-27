<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\SiteSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Invoices, quotations, and receipts must fit on a single A4 page. The
 * customer-facing invoice/quote (invoices/pdf.blade.php) and the paid
 * receipt (receipts/pdf.blade.php) previously used a bloated "email card"
 * layout — huge padding, oversized fonts, and a 112px fixed margin before
 * the footer — that pushed even a single-line-item document onto a second
 * page. These tests render a realistic invoice/quote/receipt through dompdf
 * and fail if it ever produces more than one page again.
 */
class PdfSinglePageLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function countPdfPages(string $pdfContent): int
    {
        return preg_match_all('/\/Type\s*\/Page[^s]/', $pdfContent);
    }

    private function makeInvoice(string $serviceType = 'catalog', string $invoiceNumber = 'INV-PAGE-0001'): Invoice
    {
        SiteSetting::query()->create(['key' => 'company_account_name', 'value' => 'Alet Inspirationz Ltd', 'group' => 'general']);
        SiteSetting::query()->create(['key' => 'company_account_number', 'value' => '0062999338', 'group' => 'general']);
        SiteSetting::query()->create(['key' => 'company_account_bank_name', 'value' => 'Access Bank', 'group' => 'general']);
        SiteSetting::query()->create(['key' => 'company_account_note', 'value' => 'Please use the invoice number as payment narration.', 'group' => 'general']);
        SiteSettings::clearCache();

        $customer = User::factory()->create(['role' => 'customer']);

        $lineItems = [];
        for ($i = 1; $i <= 4; $i++) {
            $lineItems[] = [
                'description' => "Custom print item #{$i} — premium finish, full colour, double sided",
                'quantity' => 100 * $i,
                'rate' => 250,
                'amount' => 100 * $i * 250,
            ];
        }

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'job_order_number' => 'PB-2026-PAGECHK',
            'customer_name' => 'Chukwuemeka Adeyemi',
            'customer_email' => 'chukwuemeka@example.com',
            'customer_phone' => '08098765432',
            'job_type' => 'Mixed Print Job',
            'quantity' => 1000,
            'unit_price' => 250,
            'total_price' => 250000,
            'channel' => 'Manual',
            'status' => 'Quote Requested',
            'payment_status' => 'Awaiting Invoice',
            'priority' => '🔴 Urgent',
            'service_type' => $serviceType,
            'delivery_address' => '42 Adeola Odeku Street, Victoria Island',
            'delivery_city' => 'Lagos',
            'pricing_breakdown' => ['line_items' => $lineItems],
        ]);

        return Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => $invoiceNumber,
            'subtotal' => 250000,
            'tax_amount' => 5000,
            'discount_amount' => 15000,
            'total_amount' => 240000,
            'status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);
    }

    public function test_customer_invoice_pdf_fits_on_a_single_page(): void
    {
        $invoice = $this->makeInvoice()->load('order.product');

        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);

        $this->assertSame(1, $this->countPdfPages($pdf->output()));
    }

    public function test_quotation_pdf_fits_on_a_single_page(): void
    {
        $invoice = $this->makeInvoice(serviceType: 'quote', invoiceNumber: 'QUO-PAGE-0001')->load('order.product');

        $pdf = Pdf::loadView('admin.invoices.pdf', ['invoice' => $invoice]);

        $this->assertSame(1, $this->countPdfPages($pdf->output()));
    }

    public function test_admin_invoice_pdf_fits_on_a_single_page(): void
    {
        $invoice = $this->makeInvoice()->load('order.product');

        $pdf = Pdf::loadView('admin.invoices.pdf', ['invoice' => $invoice]);

        $this->assertSame(1, $this->countPdfPages($pdf->output()));
    }

    public function test_paid_receipt_pdf_fits_on_a_single_page(): void
    {
        $invoice = $this->makeInvoice();
        $invoice->forceFill(['status' => 'paid', 'paid_at' => now(), 'payment_gateway' => 'paystack', 'payment_reference' => 'TXN123456'])->save();
        $invoice->load('order.product');

        $pdf = Pdf::loadView('receipts.pdf', ['invoice' => $invoice]);

        $this->assertSame(1, $this->countPdfPages($pdf->output()));
    }
}
