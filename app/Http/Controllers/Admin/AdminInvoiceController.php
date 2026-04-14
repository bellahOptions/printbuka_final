<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
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

    public function store(Request $request): RedirectResponse
    {
        Invoice::query()->create($this->validated($request));

        return redirect()->route('admin.invoices.index')->with('status', 'Invoice created.');
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
        return $request->validate([
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
    }
}
