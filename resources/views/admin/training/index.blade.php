@extends('layouts.admin')

@section('title', 'Training Applications')

@php
    $badgeClass = function (string $status): string {
        return match ($status) {
            \App\Models\Training::STATUS_ACCEPTED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            \App\Models\Training::STATUS_REJECTED => 'bg-pink-50 text-pink-700 border-pink-200',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    };
@endphp

@section('content')
    <section class="space-y-6">
        <div class="pb-page-header">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">PGTP Applications</p>
                <h1 class="pb-page-title mt-2 text-4xl">Training submissions.</h1>
                <p class="pb-page-subtitle">Review applicants and send acceptance or rejection decisions.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="pb-alert pb-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([['Total', $stats['total'], 'text-slate-950'], ['Pending', $stats['pending'], 'text-amber-700'], ['Accepted', $stats['accepted'], 'text-emerald-700'], ['Rejected', $stats['rejected'], 'text-pink-700']] as [$label, $value, $class])
                <div class="pb-stat-card">
                    <p class="pb-stat-label">{{ $label }}</p>
                    <p class="pb-stat-value {{ $class }}">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.training.index') }}" class="pb-card grid gap-3 p-4 md:grid-cols-[1fr_220px_220px_auto]">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search name, email, phone..." class="pb-input">
            <select name="status" class="pb-select">
                <option value="">All statuses</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="skill" class="pb-select">
                <option value="">All skills</option>
                @foreach ($skills as $skillOption)
                    <option value="{{ $skillOption }}" @selected($skill === $skillOption)>{{ $skillOption }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="pb-btn pb-btn-ink">Filter</button>
                <a href="{{ route('admin.training.index') }}" class="pb-btn pb-btn-outline">Reset</a>
            </div>
        </form>

        <livewire:admin.training-applications-table :filters="['status' => $status, 'skill' => $skill, 'search' => $search]" />
    </section>
@endsection
