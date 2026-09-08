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
    <livewire:admin.payroll-runs-table />

</div>
@endsection
