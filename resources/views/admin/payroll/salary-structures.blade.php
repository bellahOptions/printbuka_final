@extends('layouts.admin')
@section('title', 'Salary Structures | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.payroll.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">← Back to Payroll</a>
            <h1 class="pb-page-title">Salary Structures</h1>
            <p class="pb-page-subtitle">Active salary configuration per staff member</p>
        </div>
    </div>

    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="pb-alert pb-alert-error flex-col items-start">
            @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    {{-- Set / update structure form --}}
    @if (auth()->user()->canAdmin('payroll.manage') || auth()->user()->canAdmin('*'))
    <div class="pb-card pb-card-content">
        <h2 class="pb-section-title mb-4">Set / Update Salary Structure</h2>
        <form method="POST" action="{{ route('admin.payroll.salary-store') }}">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="pb-field sm:col-span-2 lg:col-span-3">
                    <label class="pb-label">Staff Member <span class="text-brand-600">*</span></label>
                    <select name="staff_id" required class="pb-select w-full">
                        <option value="">Select staff...</option>
                        @foreach ($staffList as $s)
                            <option value="{{ $s->id }}" @selected(old('staff_id') == $s->id)>{{ $s->displayName() }} ({{ ucwords(str_replace('_', ' ', $s->role)) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="pb-field">
                    <label class="pb-label">Effective Date <span class="text-brand-600">*</span></label>
                    <input type="date" name="effective_date" value="{{ old('effective_date', now()->format('Y-m-d')) }}" required class="pb-input w-full">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Basic Salary (₦) <span class="text-brand-600">*</span></label>
                    <input type="number" name="basic_salary" value="{{ old('basic_salary') }}" step="0.01" min="0" required class="pb-input w-full">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Housing Allowance (₦)</label>
                    <input type="number" name="housing_allowance" value="{{ old('housing_allowance', 0) }}" step="0.01" min="0" class="pb-input w-full">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Transport Allowance (₦)</label>
                    <input type="number" name="transport_allowance" value="{{ old('transport_allowance', 0) }}" step="0.01" min="0" class="pb-input w-full">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Medical Allowance (₦)</label>
                    <input type="number" name="medical_allowance" value="{{ old('medical_allowance', 0) }}" step="0.01" min="0" class="pb-input w-full">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Other Allowances (₦)</label>
                    <input type="number" name="other_allowances" value="{{ old('other_allowances', 0) }}" step="0.01" min="0" class="pb-input w-full">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Pension Deduction (₦)</label>
                    <input type="number" name="pension_deduction" value="{{ old('pension_deduction', 0) }}" step="0.01" min="0" class="pb-input w-full">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Tax / PAYE (₦)</label>
                    <input type="number" name="tax_deduction" value="{{ old('tax_deduction', 0) }}" step="0.01" min="0" class="pb-input w-full">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Other Deductions (₦)</label>
                    <input type="number" name="other_deductions" value="{{ old('other_deductions', 0) }}" step="0.01" min="0" class="pb-input w-full">
                </div>
                <div class="pb-field sm:col-span-2 lg:col-span-3">
                    <label class="pb-label">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Optional notes for this salary update..." class="pb-input w-full">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit" class="pb-btn pb-btn-md pb-btn-ink">Save Salary Structure</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Current structures --}}
    <div class="pb-card overflow-hidden">
        <div class="pb-card-header border-b border-slate-100">
            <h2 class="pb-card-title">Active Structures</h2>
        </div>
        <div class="pb-table-wrapper border-0 rounded-none">
            <table class="pb-table pb-table--cards">
                <thead>
                    <tr>
                        <th>Staff</th>
                        <th class="text-right">Basic</th>
                        <th class="text-right">Allowances</th>
                        <th class="text-right">Gross</th>
                        <th class="text-right">Deductions</th>
                        <th class="text-right">Net</th>
                        <th>Since</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($structures as $ss)
                    <tr>
                        <td data-label="Staff">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $ss->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $ss->staff?->displayName() }}</p>
                                    <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $ss->staff?->role ?? '')) }}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Basic" class="text-right">₦{{ number_format($ss->basic_salary, 0) }}</td>
                        <td data-label="Allowances" class="text-right">₦{{ number_format($ss->housing_allowance + $ss->transport_allowance + $ss->medical_allowance + $ss->other_allowances, 0) }}</td>
                        <td data-label="Gross" class="text-right font-semibold text-slate-900">₦{{ number_format($ss->grossSalary(), 0) }}</td>
                        <td data-label="Deductions" class="text-right text-brand-600">-₦{{ number_format($ss->totalDeductions(), 0) }}</td>
                        <td data-label="Net" class="text-right font-semibold text-emerald-700">₦{{ number_format($ss->netSalary(), 0) }}</td>
                        <td data-label="Since" class="text-xs text-slate-500">{{ $ss->effective_date->format('M j, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="pb-empty"><p class="pb-empty-title">No salary structures set yet.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
