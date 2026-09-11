@extends('layouts.admin')
@section('title', 'Create Payroll Run | Printbuka')

@section('content')
<div class="mx-auto max-w-xl space-y-6">

    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.payroll.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">← Back to Payroll</a>
            <h1 class="pb-page-title">Create Payroll Run</h1>
            <p class="pb-page-subtitle">Auto-generates payroll entries for all active staff with salary structures.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="pb-alert pb-alert-error flex-col items-start">
            @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="pb-card pb-card-content">
        <form method="POST" action="{{ route('admin.payroll.store-run') }}">
            @csrf
            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Month <span class="text-brand-600">*</span></label>
                        <select name="payroll_month" required class="pb-select w-full">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected(old('payroll_month', now()->month) == $m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Year <span class="text-brand-600">*</span></label>
                        <select name="payroll_year" required class="pb-select w-full">
                            @foreach (range(now()->year + 1, now()->year - 2) as $y)
                                <option value="{{ $y }}" @selected(old('payroll_year', now()->year) == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pb-field">
                    <label class="pb-label">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date') }}" class="pb-input w-full">
                    <p class="text-xs text-slate-400 mt-1">Expected payment date for this cycle. Can be updated later.</p>
                </div>

                <div class="pb-field">
                    <label class="pb-label">Notes</label>
                    <textarea name="notes" rows="2" data-rich-editor placeholder="Optional notes for this payroll run..." class="pb-textarea w-full">{{ old('notes') }}</textarea>
                </div>

                <div class="pb-alert pb-alert-warning">
                    This will auto-generate payroll entries for all <strong>{{ $activeStaffCount }}</strong> active staff members who have an active salary structure. Only one run per month/year is allowed.
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-ink">Generate Payroll Run</button>
                    <a href="{{ route('admin.payroll.index') }}" class="pb-btn pb-btn-md pb-btn-outline">Cancel</a>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
