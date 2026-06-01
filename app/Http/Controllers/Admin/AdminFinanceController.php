<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceEntry;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFinanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = FinanceEntry::query()
            ->with('order', 'recorder');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->input('entry_type'));
        }
        if ($request->filled('category')) {
            $query->where('category', 'like', '%'.$request->input('category').'%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->input('date_to'));
        }

        return view('admin.finance.index', [
            'entries' => $query->orderByDesc('entry_date')
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString(),
            'income' => FinanceEntry::query()->where('type', 'income')->sum('amount'),
            'expenses' => FinanceEntry::query()->where('type', 'expense')->sum('amount'),
        ]);
    }

    public function show(FinanceEntry $finance): View
    {
        return view('admin.finance.show', [
            'entry' => $finance->load('order', 'recorder'),
        ]);
    }

    public function reportForm(Request $request): View
    {
        $types = FinanceEntry::query()
            ->select('type')
            ->distinct()
            ->pluck('type');

        $entryTypes = FinanceEntry::query()
            ->select('entry_type')
            ->whereNotNull('entry_type')
            ->distinct()
            ->pluck('entry_type');

        $categories = FinanceEntry::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.finance.report-form', [
            'types' => $types,
            'entryTypes' => $entryTypes,
            'categories' => $categories,
        ]);
    }

    public function downloadReport(Request $request)
    {
        $request->validate([
            'period' => ['required', 'in:weekly,monthly,custom'],
            'type' => ['nullable', 'in:income,expense'],
            'entry_type' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date', 'required_if:period,custom'],
            'date_to' => ['nullable', 'date', 'required_if:period,custom', 'after_or_equal:date_from'],
        ]);

        $query = FinanceEntry::query()->with('order', 'recorder');

        // Date range
        if ($request->period === 'weekly') {
            $query->whereDate('entry_date', '>=', now()->startOfWeek())
                  ->whereDate('entry_date', '<=', now()->endOfWeek());
        } elseif ($request->period === 'monthly') {
            $query->whereMonth('entry_date', now()->month)
                  ->whereYear('entry_date', now()->year);
        } elseif ($request->period === 'custom') {
            if ($request->filled('date_from')) {
                $query->whereDate('entry_date', '>=', $request->date('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('entry_date', '<=', $request->date('date_to'));
            }
        }

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->input('entry_type'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $entries = $query->orderByDesc('entry_date')->orderByDesc('created_at')->get();

        $incomeTotal = $entries->where('type', 'income')->sum('amount');
        $expenseTotal = $entries->where('type', 'expense')->sum('amount');
        $netTotal = $incomeTotal - $expenseTotal;

        $generatedBy = $request->user();

        $pdf = Pdf::loadView('admin.finance.report-pdf', [
            'entries' => $entries,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'netTotal' => $netTotal,
            'period' => $request->period,
            'dateFrom' => $request->date('date_from'),
            'dateTo' => $request->date('date_to'),
            'generatedBy' => $generatedBy,
        ]);

        $periodLabel = match ($request->period) {
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'custom' => 'Report',
        };

        return $pdf->download('finance-'.strtolower($periodLabel).'-'.now()->format('Y-m-d').'.pdf');
    }

    public function download(FinanceEntry $finance)
    {
        $entry = $finance->load('order', 'recorder');

        $pdf = Pdf::loadView('admin.finance.pdf', [
            'entry' => $entry,
        ]);

        return $pdf->download('finance-entry-'.$entry->id.'.pdf');
    }

    public function create(): View
    {
        return view('admin.finance.form', [
            'entry' => new FinanceEntry(['entry_date' => now(), 'type' => 'expense']),
            'orders' => Order::query()->latest()->get(),
            'isAutoIncome' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FinanceEntry::query()->create([
            ...$this->validatedManualExpense($request),
            'user_id' => $request->user()->id,
        ]);
            if ($request->input('type') === 'income') {
                return redirect()->route('admin.finance.index')
                    ->with('status', 'Income entry created. Note: Income entries are typically generated automatically from paid invoices, check notes for details of the generated entry.');
            }
        return redirect()->route('admin.finance.index')->with('status', 'Expense entry created.');
    }

    public function edit(FinanceEntry $finance): View
    {
        return view('admin.finance.form', [
            'entry' => $finance,
            'orders' => Order::query()->latest()->get(),
            'isAutoIncome' => $finance->type === 'income',
        ]);
    }

    public function update(Request $request, FinanceEntry $finance): RedirectResponse
    {
        if ($finance->type === 'income') {
            return redirect()->route('admin.finance.index')
                ->with('warning', 'Income entries are generated automatically from paid invoices and cannot be edited manually.');
        }

        $finance->update([
            ...$this->validatedManualExpense($request),
            'type' => 'expense',
        ]);

        return redirect()->route('admin.finance.index')->with('status', 'Expense entry updated.');
    }

    public function destroy(FinanceEntry $finance): RedirectResponse
    {
        if ($finance->type === 'income') {
            return back()->with('warning', 'Income entries are generated automatically from paid invoices and cannot be deleted manually.');
        }

        $finance->delete();

        return back()->with('status', 'Expense entry deleted.');
    }

    private function validatedManualExpense(Request $request): array
    {
        return $request->validate([
            'order_id' => ['nullable', 'exists:orders,id'],
            'entry_date' => ['required', 'date'],
            'type' => ['required', 'in:expense,income'],
            'entry_type' => ['nullable', 'string', 'max:50'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'payee' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

