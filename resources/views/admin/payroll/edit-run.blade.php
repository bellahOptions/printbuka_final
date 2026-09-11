@extends('layouts.admin')
@section('title', 'Edit '.$run->periodLabel().' Payroll Run | Printbuka')

@section('content')
<div class="mx-auto max-w-xl space-y-6">

    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.payroll.run', $run) }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">← Back to Payroll Run</a>
            <h1 class="pb-page-title">Edit Payroll Run</h1>
            <p class="pb-page-subtitle">Only the period, payment date, and notes can be changed here. To adjust individual staff amounts, edit them from the run page.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="pb-alert pb-alert-error flex-col items-start">
            @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="pb-card pb-card-content">
        <form method="POST" action="{{ route('admin.payroll.update-run', $run) }}">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Month <span class="text-brand-600">*</span></label>
                        <select name="payroll_month" required class="pb-select w-full">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected(old('payroll_month', $run->payroll_month) == $m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Year <span class="text-brand-600">*</span></label>
                        <select name="payroll_year" required class="pb-select w-full">
                            @foreach (range(now()->year + 1, now()->year - 2) as $y)
                                <option value="{{ $y }}" @selected(old('payroll_year', $run->payroll_year) == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pb-field">
                    <label class="pb-label">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', $run->payment_date?->format('Y-m-d')) }}" class="pb-input w-full">
                </div>

                <div class="pb-field">
                    <label class="pb-label">Notes</label>
                    <textarea name="notes" rows="3" class="pb-textarea w-full">{{ old('notes', $run->notes) }}</textarea>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-ink">Save Changes</button>
                    <a href="{{ route('admin.payroll.run', $run) }}" class="pb-btn pb-btn-md pb-btn-outline">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <div class="pb-card pb-card-content border-red-200 bg-red-50">
        <p class="pb-section-title text-red-900">Cancel This Payroll Run</p>
        <p class="text-xs text-red-700 mt-1 mb-4">Permanently deletes this run and all {{ $run->entries()->count() }} staff entries in it. This cannot be undone.</p>
        <form method="POST" action="{{ route('admin.payroll.destroy-run', $run) }}" onsubmit="return confirm('Cancel and permanently delete the payroll run for {{ $run->periodLabel() }}? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="pb-btn pb-btn-md pb-btn-destructive">Cancel Payroll Run</button>
        </form>
    </div>

</div>
@endsection
