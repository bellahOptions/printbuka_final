@extends('layouts.admin')
@section('title', $run->periodLabel().' Payroll Run | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.payroll.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Payroll</a>
            <div class="flex items-center gap-4 mt-3">
                <h1 class="text-2xl font-black text-slate-950">{{ $run->periodLabel() }}</h1>
                <span class="rounded-full px-3 py-1 text-xs font-black {{ $run->statusBadgeClass() }}">{{ ucfirst($run->status) }}</span>
            </div>
            <p class="text-sm text-slate-500 mt-1">{{ $run->entries->count() }} entries · Total Net: ₦{{ number_format($run->totalNetPayroll(), 2) }}</p>
        </div>
        <div class="flex gap-2 flex-wrap justify-end">
            {{-- Download PDF — available to anyone with payroll.view --}}
            <a href="{{ route('admin.payroll.run.download', $run) }}"
               class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-50 flex items-center gap-1.5">
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
                    <button type="submit" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-50 flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Send to CEO
                    </button>
                </form>

                @if ($run->status === 'draft')
                    <form method="POST" action="{{ route('admin.payroll.finalize', $run) }}" onsubmit="return confirm('Finalize this payroll run? No more edits after this.')">
                        @csrf
                        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-black text-white hover:bg-slate-700">Finalize Run</button>
                    </form>
                @elseif ($run->status === 'finalized')
                    <form method="POST" action="{{ route('admin.payroll.send-payslips', $run) }}" onsubmit="return confirm('Send payslip emails to all {{ $run->entries->count() }} staff?')">
                        @csrf
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-black text-white hover:bg-emerald-700">Send Payslips &amp; Mark Paid</button>
                    </form>
                @endif
            @endif
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

    {{-- Totals summary --}}
    @php
    $totalGross = $run->entries->sum('gross_salary');
    $totalDeductions = $run->entries->sum('total_deductions');
    $totalNet = $run->entries->sum('net_salary');
    @endphp
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-2xl font-black text-slate-900">₦{{ number_format($totalGross, 0) }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mt-1">Total Gross</p>
        </div>
        <div class="rounded-2xl border border-pink-100 bg-pink-50 p-5 shadow-sm">
            <p class="text-2xl font-black text-pink-700">₦{{ number_format($totalDeductions, 0) }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-pink-500 mt-1">Total Deductions</p>
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
            <p class="text-2xl font-black text-emerald-700">₦{{ number_format($totalNet, 0) }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-emerald-600 mt-1">Total Net Payroll</p>
        </div>
    </div>

    {{-- Entries table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-black text-slate-950">Staff Entries</h2>
        </div>
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Staff</th>
                    <th class="px-5 py-3.5 text-right">Gross</th>
                    <th class="px-5 py-3.5 text-right">Deductions</th>
                    <th class="px-5 py-3.5 text-right">Net Pay</th>
                    <th class="px-5 py-3.5 text-left">Method</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($run->entries->load('staff') as $entry)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $entry->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                            <div>
                                <p class="text-sm font-black text-slate-900">{{ $entry->staff?->displayName() }}</p>
                                <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $entry->staff?->role ?? '')) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm font-semibold text-slate-800 text-right">₦{{ number_format($entry->gross_salary, 2) }}</td>
                    <td class="px-5 py-4 text-sm font-semibold text-pink-600 text-right">-₦{{ number_format($entry->total_deductions, 2) }}</td>
                    <td class="px-5 py-4 text-sm font-black text-slate-900 text-right">₦{{ number_format($entry->net_salary, 2) }}</td>
                    <td class="px-5 py-4">
                        @if ($run->status === 'draft')
                            <form method="POST" action="{{ route('admin.payroll.update-entry', $entry) }}" class="flex gap-2 items-center">
                                @csrf @method('PATCH')
                                <select name="payment_method" class="rounded-lg border border-slate-300 px-2 py-1 text-xs focus:outline-none">
                                    <option value="">Select</option>
                                    @foreach (['Bank Transfer','Cash','Cheque'] as $pm)
                                        <option @selected($entry->payment_method === $pm)>{{ $pm }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs font-black text-pink-600 hover:underline">Save</button>
                            </form>
                        @else
                            <span class="text-sm text-slate-600">{{ $entry->payment_method ?? '—' }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $entry->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($entry->payment_status) }}</span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.payroll.payslip.download', $entry) }}" class="text-xs font-black text-slate-600 hover:text-pink-600 hover:underline">PDF</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($run->notes)
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-1">Notes</p>
        <p class="text-sm text-slate-700">{{ $run->notes }}</p>
    </div>
    @endif

</div>
@endsection
