<?php

namespace App\Http\Controllers\Local;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class InvoiceDesignPreviewController extends Controller
{
    public function index(): View
    {
        return view('previews.invoice-designs');
    }

    public function invoicePdf(): View
    {
        return view('invoices.pdf', [
            'invoice' => $this->previewInvoice(),
        ]);
    }

    public function receiptPdf(): View
    {
        $invoice = $this->previewInvoice();
        $invoice->setAttribute('status', 'paid');
        $invoice->setAttribute('paid_at', now()->subDay());

        return view('receipts.pdf', [
            'invoice' => $invoice,
        ]);
    }

    public function invoiceEmail(): View
    {
        return view('mail.invoices.created', [
            'invoice' => $this->previewInvoice(),
        ]);
    }

    public function paidReceiptEmail(): View
    {
        $invoice = $this->previewInvoice();
        $invoice->setAttribute('status', 'paid');
        $invoice->setAttribute('paid_at', now()->subDay());

        return view('mail.invoices.paid-receipt', [
            'invoice' => $invoice,
        ]);
    }

    private function previewInvoice(): Invoice
    {
        $invoice = Invoice::query()
            ->with('order.product')
            ->latest('id')
            ->first();

        if ($invoice) {
            return $invoice;
        }

        $product = new Product([
            'name' => 'Premium Business Cards',
        ]);
        $product->setAttribute('id', 1);

        $order = new Order([
            'service_type' => 'Printing',
            'job_type' => 'Business Cards',
            'quantity' => 500,
            'unit_price' => 250,
            'customer_name' => 'Alexandra Chen',
            'customer_email' => 'alexandra@example.com',
            'customer_phone' => '+1 (212) 555-0198',
            'delivery_city' => 'Lagos',
            'delivery_address' => '451 West 24th Street, Apt 8B',
            'amount_paid' => 0,
            'payment_status' => 'Invoice Issued',
            'estimated_delivery_at' => now()->addDays(4),
            'job_order_number' => 'JOB-2026-0382',
            'pricing_breakdown' => [
                'line_items' => [
                    [
                        'description' => 'Brand identity and strategy package',
                        'quantity' => 1,
                        'rate' => 3200,
                        'amount' => 3200,
                    ],
                    [
                        'description' => 'Web design (UI/UX) 6 pages + responsive',
                        'quantity' => 1,
                        'rate' => 4850,
                        'amount' => 4850,
                    ],
                    [
                        'description' => 'Development and CMS setup',
                        'quantity' => 1,
                        'rate' => 5200,
                        'amount' => 5200,
                    ],
                    [
                        'description' => 'Copywriting and SEO setup',
                        'quantity' => 1,
                        'rate' => 950,
                        'amount' => 950,
                    ],
                ],
            ],
        ]);
        $order->setAttribute('id', 382);
        $order->setRelation('product', $product);

        $invoice = new Invoice([
            'invoice_number' => 'INV-2026-0382',
            'payment_reference' => 'tr_9x7LmNq42P',
            'payment_gateway' => 'paystack',
            'subtotal' => 14200,
            'tax_amount' => 1207,
            'discount_amount' => 0,
            'total_amount' => 15407,
            'status' => 'unpaid',
            'issued_at' => now()->subDays(2),
            'due_at' => now()->addDays(7),
        ]);
        $invoice->setAttribute('id', 382);
        $invoice->setRelation('order', $order);

        return $invoice;
    }
}
