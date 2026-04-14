<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminInvoiceController extends Controller
{
    public function index(): View
    {
        return view('admin.invoices.index', [
            'invoices' => Invoice::query()->with('order')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.invoices.form', [
            'invoice' => new Invoice(['status' => 'draft', 'issued_at' => now()]),
            'orders' => Order::query()->latest()->get(),
        ]);
    }

    public function store(Request $request, InvoiceService $invoiceService): RedirectResponse
    {
        $invoice = Invoice::query()->create($this->validated($request));
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
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update($this->validated($request, $invoice));

        return redirect()->route('admin.invoices.index')->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return back()->with('status', 'Invoice deleted.');
    }

    private function validated(Request $request, ?Invoice $invoice = null): array
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'invoice_number' => ['required', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($invoice?->id)],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:255'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'sent_at' => ['nullable', 'date'],
        ]);

        $validated['tax_amount'] ??= 0;
        $validated['discount_amount'] ??= 0;

        return $validated;
    }
}
