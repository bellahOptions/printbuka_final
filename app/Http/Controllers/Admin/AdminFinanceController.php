<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceEntry;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminFinanceController extends Controller
{
    public function index(): View
    {
        return view('admin.finance.index', [
            'entries' => FinanceEntry::query()->with('order', 'recorder')->latest('entry_date')->paginate(20),
            'income' => FinanceEntry::query()->where('type', 'income')->sum('amount'),
            'expenses' => FinanceEntry::query()->where('type', 'expense')->sum('amount'),
        ]);
    }

    public function create(): View
    {
        return view('admin.finance.form', [
            'entry' => new FinanceEntry(['entry_date' => now(), 'type' => 'expense']),
            'orders' => Order::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FinanceEntry::query()->create([
            ...$this->validated($request),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('admin.finance.index')->with('status', 'Finance entry created.');
    }

    public function edit(FinanceEntry $finance): View
    {
        return view('admin.finance.form', [
            'entry' => $finance,
            'orders' => Order::query()->latest()->get(),
        ]);
    }

    public function update(Request $request, FinanceEntry $finance): RedirectResponse
    {
        $finance->update($this->validated($request));

        return redirect()->route('admin.finance.index')->with('status', 'Finance entry updated.');
    }

    public function destroy(FinanceEntry $finance): RedirectResponse
    {
        $finance->delete();

        return back()->with('status', 'Finance entry deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'order_id' => ['nullable', 'exists:orders,id'],
            'entry_date' => ['required', 'date'],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'payee' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
