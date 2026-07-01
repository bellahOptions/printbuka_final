@extends('layouts.admin')
@section('title', 'Create Payroll Run | Printbuka')

@section('content')
<div class="mx-auto max-w-xl space-y-6">

    <div>
        <a href="{{ route('admin.payroll.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Payroll</a>
        <h1 class="text-2xl font-black text-slate-950 mt-3">Create Payroll Run</h1>
        <p class="text-sm text-slate-500 mt-1">Auto-generates payroll entries for all active staff with salary structures.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
            @foreach ($errors->all() as $e)<p class="text-sm font-semibold text-pink-700">{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.payroll.store-run') }}">
            @csrf
            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Month <span class="text-pink-600">*</span></label>
                        <select name="payroll_month" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected(old('payroll_month', now()->month) == $m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Year <span class="text-pink-600">*</span></label>
                        <select name="payroll_year" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                            @foreach (range(now()->year + 1, now()->year - 2) as $y)
                                <option value="{{ $y }}" @selected(old('payroll_year', now()->year) == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                    <p class="text-xs text-slate-400 mt-1">Expected payment date for this cycle. Can be updated later.</p>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Optional notes for this payroll run..." class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">{{ old('notes') }}</textarea>
                </div>

                <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
                    <p class="text-xs font-black text-amber-800">This will auto-generate payroll entries for all <strong>{{ $activeStaffCount }}</strong> active staff members who have an active salary structure. Only one run per month/year is allowed.</p>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-black text-white hover:bg-slate-700">Generate Payroll Run</button>
                    <a href="{{ route('admin.payroll.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
