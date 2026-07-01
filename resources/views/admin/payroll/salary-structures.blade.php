@extends('layouts.admin')
@section('title', 'Salary Structures | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.payroll.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Payroll</a>
            <h1 class="text-2xl font-black text-slate-950 mt-3">Salary Structures</h1>
            <p class="text-sm text-slate-500 mt-1">Active salary configuration per staff member</p>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
            @foreach ($errors->all() as $e)<p class="text-sm font-semibold text-pink-700">{{ $e }}</p>@endforeach
        </div>
    @endif

    {{-- Set / update structure form --}}
    @if (auth()->user()->canAdmin('payroll.manage') || auth()->user()->canAdmin('*'))
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-black text-slate-950 mb-4">Set / Update Salary Structure</h2>
        <form method="POST" action="{{ route('admin.payroll.salary-store') }}">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Staff Member <span class="text-pink-600">*</span></label>
                    <select name="staff_id" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                        <option value="">Select staff...</option>
                        @foreach ($staffList as $s)
                            <option value="{{ $s->id }}" @selected(old('staff_id') == $s->id)>{{ $s->displayName() }} ({{ ucwords(str_replace('_', ' ', $s->role)) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Effective Date <span class="text-pink-600">*</span></label>
                    <input type="date" name="effective_date" value="{{ old('effective_date', now()->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Basic Salary (₦) <span class="text-pink-600">*</span></label>
                    <input type="number" name="basic_salary" value="{{ old('basic_salary') }}" step="0.01" min="0" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Housing Allowance (₦)</label>
                    <input type="number" name="housing_allowance" value="{{ old('housing_allowance', 0) }}" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Transport Allowance (₦)</label>
                    <input type="number" name="transport_allowance" value="{{ old('transport_allowance', 0) }}" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Medical Allowance (₦)</label>
                    <input type="number" name="medical_allowance" value="{{ old('medical_allowance', 0) }}" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Other Allowances (₦)</label>
                    <input type="number" name="other_allowances" value="{{ old('other_allowances', 0) }}" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Pension Deduction (₦)</label>
                    <input type="number" name="pension_deduction" value="{{ old('pension_deduction', 0) }}" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Tax / PAYE (₦)</label>
                    <input type="number" name="tax_deduction" value="{{ old('tax_deduction', 0) }}" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Other Deductions (₦)</label>
                    <input type="number" name="other_deductions" value="{{ old('other_deductions', 0) }}" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Optional notes for this salary update..." class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-black text-white hover:bg-slate-700">Save Salary Structure</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Current structures --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-black text-slate-950">Active Structures</h2>
        </div>
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Staff</th>
                    <th class="px-5 py-3.5 text-right">Basic</th>
                    <th class="px-5 py-3.5 text-right">Allowances</th>
                    <th class="px-5 py-3.5 text-right">Gross</th>
                    <th class="px-5 py-3.5 text-right">Deductions</th>
                    <th class="px-5 py-3.5 text-right">Net</th>
                    <th class="px-5 py-3.5 text-left">Since</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($structures as $ss)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $ss->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                            <div>
                                <p class="text-sm font-black text-slate-900">{{ $ss->staff?->displayName() }}</p>
                                <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $ss->staff?->role ?? '')) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm font-semibold text-slate-700 text-right">₦{{ number_format($ss->basic_salary, 0) }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600 text-right">₦{{ number_format($ss->housing_allowance + $ss->transport_allowance + $ss->medical_allowance + $ss->other_allowances, 0) }}</td>
                    <td class="px-5 py-4 text-sm font-black text-slate-900 text-right">₦{{ number_format($ss->grossSalary(), 0) }}</td>
                    <td class="px-5 py-4 text-sm font-semibold text-pink-600 text-right">-₦{{ number_format($ss->totalDeductions(), 0) }}</td>
                    <td class="px-5 py-4 text-sm font-black text-emerald-700 text-right">₦{{ number_format($ss->netSalary(), 0) }}</td>
                    <td class="px-5 py-4 text-xs text-slate-500">{{ $ss->effective_date->format('M j, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No salary structures set yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
