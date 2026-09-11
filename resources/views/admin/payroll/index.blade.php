@extends('layouts.admin')
@section('title', 'Payroll | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="pb-page-header">
        <div>
            <h1 class="pb-page-title">Payroll</h1>
            <p class="pb-page-subtitle">Monthly payroll runs and salary management</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.payroll.salary-structures') }}" class="pb-btn pb-btn-md pb-btn-outline">Salary Structures</a>
            @if (auth()->user()->canAdmin('payroll.manage') || auth()->user()->canAdmin('*'))
                <a href="{{ route('admin.payroll.create-run') }}" class="pb-btn pb-btn-md pb-btn-ink">+ New Payroll Run</a>
            @endif
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="pb-stat-card">
            <p class="pb-stat-value">{{ $totalRuns }}</p>
            <p class="pb-stat-label mt-1">Total Runs</p>
        </div>
        <div class="pb-stat-card">
            <p class="pb-stat-value text-emerald-700">{{ $paidRuns }}</p>
            <p class="pb-stat-label mt-1">Paid Runs</p>
        </div>
        <div class="pb-stat-card">
            <p class="pb-stat-value text-amber-600">{{ $draftRuns }}</p>
            <p class="pb-stat-label mt-1">Draft / Pending</p>
        </div>
    </div>

    {{-- Runs table --}}
    <livewire:admin.payroll-runs-table />

</div>
@endsection
