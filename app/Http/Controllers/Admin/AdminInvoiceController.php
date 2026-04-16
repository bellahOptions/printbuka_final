<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceLifecycleService;
use App\Services\InvoiceService;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminInvoiceController extends Controller
{
    /**
     * @return array<int, string>
     */
    private function allowedInvoiceStatuses(): array
    {
        return ['unpaid', 'paid', 'disputed'];
    }

    public function index(): View
    {
        return view('admin.invoices.index', [
            'invoices' => Invoice::query()->with('order')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.invoices.form', [
            'invoice' => new Invoice([
                'status' => 'unpaid',
                'issued_at' => now(),
                'invoice_number' => ReferenceCode::invoiceNumber(),
            ]),
            'orders' => Order::query()->latest()->get(),
            'invoiceStatuses' => $this->allowedInvoiceStatuses(),
        ]);
    }

    public function createQuotation(): View
    {
        return view('admin.invoices.quotation', [
            'customers' => User::query()
                ->where('role', 'customer')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
            'jobTypes' => config('printbuka_admin.job_types'),
            'sizes' => config('printbuka_admin.sizes'),
        ]);
    }

    public function store(
        Request $request,
        InvoiceService $invoiceService,
        InvoiceLifecycleService $invoiceLifecycleService
    ): RedirectResponse
    {
        $invoice = Invoice::query()->create($this->validated($request));
        $invoiceLifecycleService->handleStatusChange($invoice);
        $sent = $invoiceService->sendInvoice($invoice->load('order.product'));

        return redirect()
            ->route('admin.invoices.index')
            ->with(
                $sent ? 'status' : 'warning',
                $sent
                    ? 'Invoice created and emailed with PDF attachment.'
                    : 'Invoice created, but the email could not be sent. Check mail configuration.'
            );
    }

    public function edit(Invoice $invoice): View
    {
        return view('admin.invoices.form', [
            'invoice' => $invoice,
            'orders' => Order::query()->latest()->get(),
            'invoiceStatuses' => $this->allowedInvoiceStatuses(),
        ]);
    }

    public function update(
        Request $request,
        Invoice $invoice,
        InvoiceLifecycleService $invoiceLifecycleService
    ): RedirectResponse
    {
        $previousStatus = (string) $invoice->status;
        $invoice->update($this->validated($request, $invoice));
        $invoiceLifecycleService->handleStatusChange($invoice->fresh(['order.product']), $previousStatus);

        return redirect()->route('admin.invoices.index')->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return back()->with('status', 'Invoice deleted.');
    }

    public function storeQuotation(Request $request, InvoiceService $invoiceService): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'customer'))],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'product_id' => ['nullable', 'exists:products,id'],
            'job_type' => ['required', 'string', 'max:255'],
            'size_format' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'due_at' => ['nullable', 'date'],
            'delivery_city' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $customer = null;

        if (filled($validated['customer_id'] ?? null)) {
            $customer = User::query()->where('role', 'customer')->find($validated['customer_id']);
        }

        $validated['tax_amount'] = (float) ($validated['tax_amount'] ?? 0);
        $validated['discount_amount'] = (float) ($validated['discount_amount'] ?? 0);
        $quantity = (int) $validated['quantity'];
        $unitPrice = (float) $validated['unit_price'];
        $subtotal = $quantity * $unitPrice;
        $total = max(0, $subtotal + $validated['tax_amount'] - $validated['discount_amount']);

        $order = null;
        $invoice = null;

        DB::transaction(function () use (&$order, &$invoice, $validated, $customer, $subtotal, $total, $request): void {
            $order = Order::query()->create([
                'product_id' => $validated['product_id'] ?? null,
                'user_id' => $customer?->id,
                'created_by_admin_id' => $request->user()?->id,
                'service_type' => 'quote',
                'channel' => 'Manual',
                'job_type' => $validated['job_type'],
                'size_format' => $validated['size_format'] ?? null,
                'quantity' => (int) $validated['quantity'],
                'unit_price' => (float) $validated['unit_price'],
                'total_price' => $subtotal,
                'customer_name' => $customer?->displayName() ?? $validated['customer_name'],
                'customer_email' => $customer?->email ?? $validated['customer_email'],
                'customer_phone' => $customer?->phone ?? $validated['customer_phone'],
                'delivery_city' => $validated['delivery_city'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'artwork_notes' => $validated['artwork_notes'] ?? null,
                'status' => 'Quote Requested',
                'job_order_number' => ReferenceCode::jobOrderNumber('quote'),
                'priority' => '🟡 Normal',
                'brief_received_by_id' => $request->user()?->id,
                'brief_received_at' => now(),
                'payment_status' => 'Awaiting Invoice',
                'internal_notes' => $validated['internal_notes'] ?? null,
            ]);

            $invoice = Invoice::query()->create([
                'order_id' => $order->id,
                'invoice_number' => ReferenceCode::invoiceNumber(),
                'subtotal' => $subtotal,
                'tax_amount' => $validated['tax_amount'],
                'discount_amount' => $validated['discount_amount'],
                'total_amount' => $total,
                'status' => 'unpaid',
                'issued_at' => now(),
                'due_at' => $validated['due_at'] ?? now()->addDays(7),
                'sent_at' => null,
            ]);
        });

        $shouldSend = $request->boolean('send_email');
        $sent = false;

        if ($shouldSend) {
            $sent = $invoiceService->sendInvoice($invoice->load('order.product'));
        }

        return redirect()
            ->route('admin.invoices.index')
            ->with(
                $sent || ! $shouldSend ? 'status' : 'warning',
                $sent
                    ? 'Quotation created and emailed successfully.'
                    : ($shouldSend ? 'Quotation created, but email could not be sent.' : 'Quotation created successfully.')
            );
    }

    private function validated(Request $request, ?Invoice $invoice = null): array
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'invoice_number' => ['nullable', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($invoice?->id)],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in($this->allowedInvoiceStatuses())],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'sent_at' => ['nullable', 'date'],
        ]);

        $validated['tax_amount'] ??= 0;
        $validated['discount_amount'] ??= 0;
        $validated['invoice_number'] = $validated['invoice_number'] ?: ($invoice?->invoice_number ?: ReferenceCode::invoiceNumber());
        $validated['status'] = strtolower((string) $validated['status']);

        return $validated;
    }
}
