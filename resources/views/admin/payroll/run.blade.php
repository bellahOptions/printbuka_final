@extends('layouts.admin')
@section('title', $run->periodLabel().' Payroll Run | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.payroll.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">← Back to Payroll</a>
            <div class="flex items-center gap-4 mt-3">
                <h1 class="pb-page-title">{{ $run->periodLabel() }}</h1>
                <span class="pb-badge {{ $run->statusBadgeClass() }}">{{ ucfirst($run->status) }}</span>
            </div>
            <p class="pb-page-subtitle">{{ $run->entries->count() }} entries · Total Net: ₦{{ number_format($run->totalNetPayroll(), 2) }}</p>
        </div>
        <div class="flex gap-2 flex-wrap justify-end">
            {{-- Download PDF — available to anyone with payroll.view --}}
            <a href="{{ route('admin.payroll.run.download', $run) }}" class="pb-btn pb-btn-md pb-btn-outline">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Download PDF
            </a>

            @if (auth()->user()->canAdmin('payroll.manage') || auth()->user()->canAdmin('*'))
                {{-- Send to CEO --}}
                <form method="POST" action="{{ route('admin.payroll.run.email-ceo', $run) }}"
                      onsubmit="return confirm('Send payroll report for {{ $run->periodLabel() }} to the CEO / Managing Director?')">
                    @csrf
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-outline">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Send to CEO
                    </button>
                </form>

                @if ($run->status === 'draft')
                    <a href="{{ route('admin.payroll.edit-run', $run) }}" class="pb-btn pb-btn-md pb-btn-outline">
                        Edit Run
                    </a>
                    <form method="POST" action="{{ route('admin.payroll.finalize', $run) }}" onsubmit="return confirm('Finalize this payroll run? No more edits after this.')">
                        @csrf
                        <button type="submit" class="pb-btn pb-btn-md pb-btn-ink">Finalize Run</button>
                    </form>
                @elseif ($run->status === 'finalized')
                    <form method="POST" action="{{ route('admin.payroll.send-payslips', $run) }}" onsubmit="return confirm('Send payslip emails to all {{ $run->entries->count() }} staff?')">
                        @csrf
                        <button type="submit" class="pb-btn pb-btn-md pb-btn-success">Send Payslips &amp; Mark Paid</button>
                    </form>
                @endif
            @endif
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

    {{-- Totals summary --}}
    @php
    $totalGross = $run->entries->sum('gross_salary');
    $totalDeductions = $run->entries->sum('total_deductions');
    $totalNet = $run->entries->sum('net_salary');
    @endphp
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="pb-stat-card">
            <p class="pb-stat-value truncate" title="₦{{ number_format($totalGross, 2) }}">{{ \App\Support\CompactNumber::currency((float) $totalGross) }}</p>
            <p class="pb-stat-label mt-1">Total Gross</p>
        </div>
        <div class="pb-stat-card border-brand-100 bg-brand-50">
            <p class="pb-stat-value text-brand-700 truncate" title="₦{{ number_format($totalDeductions, 2) }}">{{ \App\Support\CompactNumber::currency((float) $totalDeductions) }}</p>
            <p class="pb-stat-label text-brand-500 mt-1">Total Deductions</p>
        </div>
        <div class="pb-stat-card border-emerald-100 bg-emerald-50">
            <p class="pb-stat-value text-emerald-700 truncate" title="₦{{ number_format($totalNet, 2) }}">{{ \App\Support\CompactNumber::currency((float) $totalNet) }}</p>
            <p class="pb-stat-label text-emerald-600 mt-1">Total Net Payroll</p>
        </div>
    </div>

    {{-- Entries table --}}
    <div class="pb-card overflow-hidden">
        <div class="pb-card-header border-b border-slate-100">
            <h2 class="pb-card-title">Staff Entries</h2>
        </div>
        <div class="pb-table-wrapper border-0 rounded-none">
            <table class="pb-table pb-table--cards">
                <thead>
                    <tr>
                        <th>Staff</th>
                        <th class="text-right">Gross</th>
                        <th class="text-right">Deductions</th>
                        <th class="text-right">Net Pay</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($run->entries->load('staff') as $entry)
                    <tr>
                        <td data-label="Staff">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $entry->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $entry->staff?->displayName() }}</p>
                                    <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $entry->staff?->role ?? '')) }}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Gross" class="text-right">₦{{ number_format($entry->gross_salary, 2) }}</td>
                        <td data-label="Deductions" class="text-right text-brand-600">-₦{{ number_format($entry->total_deductions, 2) }}</td>
                        <td data-label="Net Pay" class="text-right font-semibold text-slate-900">₦{{ number_format($entry->net_salary, 2) }}</td>
                        <td data-label="Method">
                            @if ($run->status === 'draft')
                                <form method="POST" action="{{ route('admin.payroll.update-entry', $entry) }}" class="flex gap-2 items-center justify-end">
                                    @csrf @method('PATCH')
                                    <select name="payment_method" class="pb-select h-8 text-xs">
                                        <option value="">Select</option>
                                        @foreach (['Bank Transfer','Cash','Cheque'] as $pm)
                                            <option @selected($entry->payment_method === $pm)>{{ $pm }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="text-xs font-semibold text-brand-600 hover:underline">Save</button>
                                </form>
                            @else
                                <span class="text-sm text-slate-600">{{ $entry->payment_method ?? '—' }}</span>
                            @endif
                        </td>
                        <td data-label="Status">
                            <span class="pb-badge {{ $entry->payment_status === 'paid' ? 'pb-badge-success' : 'pb-badge-secondary' }}">{{ ucfirst($entry->payment_status) }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.payroll.payslip.download', $entry) }}" class="text-xs font-semibold text-slate-600 hover:text-brand-600 hover:underline">PDF</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($run->notes)
    <div class="pb-card pb-card-content">
        <p class="pb-label">Notes</p>
        <p class="text-sm text-slate-700">{{ $run->notes }}</p>
    </div>
    @endif

</div>
@endsection
