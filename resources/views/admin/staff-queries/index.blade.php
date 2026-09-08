@extends('layouts.admin')
@section('title', 'Staff Queries | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Staff Queries</h1>
            <p class="text-sm text-slate-500 mt-1">Formal disciplinary queries and responses</p>
        </div>
        @if (auth()->user()->canAdmin('staff.queries') || auth()->user()->canAdmin('*'))
            <a href="{{ route('admin.staff-queries.create') }}" class="rounded-xl bg-pink-600 px-5 py-2.5 text-sm font-black text-white hover:bg-pink-700 shadow-sm">Issue Query</a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Status</label>
                <select name="status" class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:border-pink-400">
                    <option value="">All Statuses</option>
                    @foreach (['pending', 'awaiting_response', 'responded', 'closed'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Type</label>
                <select name="type" class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:border-pink-400">
                    <option value="">All Types</option>
                    @foreach (\App\Models\StaffQuery::$types as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, ref, subject..." class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:border-pink-400">
            </div>
            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-black text-white hover:bg-slate-700">Filter</button>
            @if (request()->anyFilled(['status','type','search']))
                <a href="{{ route('admin.staff-queries.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <livewire:admin.staff-query-list :filters="$filters" />

</div>
@endsection
