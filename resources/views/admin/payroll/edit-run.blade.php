@extends('layouts.admin')
@section('title', 'Edit '.$run->periodLabel().' Payroll Run | Printbuka')

@section('content')
<div class="mx-auto max-w-xl space-y-6">

    <div>
        <a href="{{ route('admin.payroll.run', $run) }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Payroll Run</a>
        <h1 class="text-2xl font-black text-slate-950 mt-3">Edit Payroll Run</h1>
        <p class="text-sm text-slate-500 mt-1">Only the period, payment date, and notes can be changed here. To adjust individual staff amounts, edit them from the run page.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
            @foreach ($errors->all() as $e)<p class="text-sm font-semibold text-pink-700">{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.payroll.update-run', $run) }}">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Month <span class="text-pink-600">*</span></label>
                        <select name="payroll_month" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected(old('payroll_month', $run->payroll_month) == $m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Year <span class="text-pink-600">*</span></label>
                        <select name="payroll_year" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                            @foreach (range(now()->year + 1, now()->year - 2) as $y)
                                <option value="{{ $y }}" @selected(old('payroll_year', $run->payroll_year) == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', $run->payment_date?->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">{{ old('notes', $run->notes) }}</textarea>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-black text-white hover:bg-slate-700">Save Changes</button>
                    <a href="{{ route('admin.payroll.run', $run) }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
        <p class="text-sm font-black text-red-900">Cancel This Payroll Run</p>
        <p class="text-xs text-red-700 mt-1 mb-4">Permanently deletes this run and all {{ $run->entries()->count() }} staff entries in it. This cannot be undone.</p>
        <form method="POST" action="{{ route('admin.payroll.destroy-run', $run) }}" onsubmit="return confirm('Cancel and permanently delete the payroll run for {{ $run->periodLabel() }}? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl border border-red-300 bg-white px-5 py-2.5 text-sm font-black text-red-700 hover:bg-red-100">Cancel Payroll Run</button>
        </form>
    </div>

</div>
@endsection
