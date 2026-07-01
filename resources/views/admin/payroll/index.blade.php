@extends('layouts.admin')
@section('title', 'Payroll | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Payroll</h1>
            <p class="text-sm text-slate-500 mt-1">Monthly payroll runs and salary management</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.payroll.salary-structures') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-black text-slate-700 hover:bg-slate-50">Salary Structures</a>
            @if (auth()->user()->canAdmin('payroll.manage') || auth()->user()->canAdmin('*'))
                <a href="{{ route('admin.payroll.create-run') }}" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-black text-white hover:bg-slate-700 shadow-sm">+ New Payroll Run</a>
            @endif
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-2xl font-black text-slate-900">{{ $totalRuns }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mt-1">Total Runs</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-2xl font-black text-emerald-700">{{ $paidRuns }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mt-1">Paid Runs</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-2xl font-black text-amber-600">{{ $draftRuns }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mt-1">Draft / Pending</p>
        </div>
    </div>

    {{-- Runs table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Period</th>
                    <th class="px-5 py-3.5 text-left">Entries</th>
                    <th class="px-5 py-3.5 text-left">Total Net Payroll</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5 text-left">Payment Date</th>
                    <th class="px-5 py-3.5 text-left">Created By</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($runs as $run)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-4 font-black text-slate-900">{{ $run->periodLabel() }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $run->entries()->count() }} staff</td>
                    <td class="px-5 py-4 font-black text-slate-900">₦{{ number_format($run->totalNetPayroll(), 2) }}</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $run->statusBadgeClass() }}">{{ ucfirst($run->status) }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $run->payment_date?->format('M j, Y') ?? '—' }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $run->createdBy?->displayName() }}</td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.payroll.run', $run) }}" class="text-sm font-black text-slate-700 hover:text-pink-600">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No payroll runs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($runs->hasPages())
        <div class="px-5 py-4 border-t border-slate-200">{{ $runs->links() }}</div>
        @endif
    </div>

</div>
@endsection
