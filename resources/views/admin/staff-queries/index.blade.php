@extends('layouts.admin')
@section('title', 'Staff Queries | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="pb-page-header">
        <div>
            <h1 class="pb-page-title">Staff Queries</h1>
            <p class="pb-page-subtitle">Formal disciplinary queries and responses</p>
        </div>
        @if (auth()->user()->canAdmin('staff.queries') || auth()->user()->canAdmin('*'))
            <a href="{{ route('admin.staff-queries.create') }}" class="pb-btn pb-btn-primary">Issue Query</a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="pb-card p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="pb-field">
                <label class="pb-label">Status</label>
                <select name="status" class="pb-select">
                    <option value="">All Statuses</option>
                    @foreach (['pending', 'awaiting_response', 'responded', 'closed'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pb-field">
                <label class="pb-label">Type</label>
                <select name="type" class="pb-select">
                    <option value="">All Types</option>
                    @foreach (\App\Models\StaffQuery::$types as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pb-field">
                <label class="pb-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, ref, subject..." class="pb-input">
            </div>
            <button type="submit" class="pb-btn pb-btn-ink">Filter</button>
            @if (request()->anyFilled(['status','type','search']))
                <a href="{{ route('admin.staff-queries.index') }}" class="pb-btn pb-btn-outline">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <livewire:admin.staff-query-list :filters="$filters" />

</div>
@endsection
