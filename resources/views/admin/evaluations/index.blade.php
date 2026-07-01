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

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Staff</th>
                    <th class="px-5 py-3.5 text-left">Period</th>
                    <th class="px-5 py-3.5 text-left">Overall</th>
                    <th class="px-5 py-3.5 text-left">Avg</th>
                    <th class="px-5 py-3.5 text-left">Evaluated By</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($evaluations as $ev)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $ev->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                            <div>
                                <p class="text-sm font-black text-slate-900">{{ $ev->staff?->displayName() }}</p>
                                <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $ev->staff?->role ?? '')) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $ev->periodLabel() }}</td>
                    <td class="px-5 py-4">
                        <span class="text-base">{{ $ev->ratingStars($ev->overall_rating) }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm font-semibold text-slate-700">{{ number_format($ev->averageRating(), 1) }}/5</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $ev->evaluatedBy?->displayName() }}</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $ev->status === 'acknowledged' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($ev->status) }}</span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.evaluations.show', $ev) }}" class="text-sm font-black text-slate-600 hover:text-pink-600">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No evaluations found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($evaluations->hasPages())
        <div class="px-5 py-4 border-t border-slate-200">{{ $evaluations->links() }}</div>
        @endif
    </div>

</div>
@endsection
