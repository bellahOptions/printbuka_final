@extends('layouts.admin')
@section('title', 'Staff Evaluations | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="pb-page-header">
        <div>
            <h1 class="pb-page-title">Performance Evaluations</h1>
            <p class="pb-page-subtitle">Monthly staff performance reviews</p>
        </div>
        @if (auth()->user()->canAdmin('staff.evaluations') || auth()->user()->canAdmin('*'))
            <a href="{{ route('admin.evaluations.create') }}" class="pb-btn pb-btn-md pb-btn-ink">+ New Evaluation</a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="pb-card pb-card-content">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="pb-field">
                <label class="pb-label">Staff</label>
                <select name="staff_id" class="pb-select">
                    <option value="">All Staff</option>
                    @foreach ($staffList as $s)
                        <option value="{{ $s->id }}" @selected(request('staff_id') == $s->id)>{{ $s->displayName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pb-field">
                <label class="pb-label">Month</label>
                <select name="month" class="pb-select">
                    <option value="">All Months</option>
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" @selected(request('month') == $m)>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pb-field">
                <label class="pb-label">Year</label>
                <select name="year" class="pb-select">
                    <option value="">All Years</option>
                    @foreach (range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="pb-btn pb-btn-md pb-btn-ink">Filter</button>
            @if (request()->anyFilled(['staff_id','month','year']))
                <a href="{{ route('admin.evaluations.index') }}" class="pb-btn pb-btn-md pb-btn-outline">Clear</a>
            @endif
        </form>
    </div>

    <livewire:admin.staff-evaluation-list :filters="$filters" />

</div>
@endsection
