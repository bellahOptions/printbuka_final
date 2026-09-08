@extends('layouts.admin')
@section('title', 'Staff Evaluations | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Performance Evaluations</h1>
            <p class="text-sm text-slate-500 mt-1">Monthly staff performance reviews</p>
        </div>
        @if (auth()->user()->canAdmin('staff.evaluations') || auth()->user()->canAdmin('*'))
            <a href="{{ route('admin.evaluations.create') }}" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-black text-white hover:bg-slate-700 shadow-sm">+ New Evaluation</a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Staff</label>
                <select name="staff_id" class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:border-pink-400">
                    <option value="">All Staff</option>
                    @foreach ($staffList as $s)
                        <option value="{{ $s->id }}" @selected(request('staff_id') == $s->id)>{{ $s->displayName() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Month</label>
                <select name="month" class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:border-pink-400">
                    <option value="">All Months</option>
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" @selected(request('month') == $m)>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Year</label>
                <select name="year" class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:border-pink-400">
                    <option value="">All Years</option>
                    @foreach (range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-black text-white hover:bg-slate-700">Filter</button>
            @if (request()->anyFilled(['staff_id','month','year']))
                <a href="{{ route('admin.evaluations.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear</a>
            @endif
        </form>
    </div>

    <livewire:admin.staff-evaluation-list :filters="$filters" />

</div>
@endsection
