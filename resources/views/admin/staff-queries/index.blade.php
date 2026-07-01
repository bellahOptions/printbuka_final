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
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Ref</th>
                    <th class="px-5 py-3.5 text-left">Staff</th>
                    <th class="px-5 py-3.5 text-left">Subject</th>
                    <th class="px-5 py-3.5 text-left">Type</th>
                    <th class="px-5 py-3.5 text-left">Date</th>
                    <th class="px-5 py-3.5 text-left">Due</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($queries as $q)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-4">
                        <span class="font-mono text-xs font-black text-pink-700">{{ $q->query_number }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $q->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                            <div>
                                <p class="text-sm font-black text-slate-900">{{ $q->staff?->displayName() }}</p>
                                <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $q->staff?->role ?? '')) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-sm font-semibold text-slate-800">{{ Str::limit($q->subject, 40) }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-sm text-slate-600">{{ $q->typeLabel() }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $q->query_date->format('M j, Y') }}</td>
                    <td class="px-5 py-4 text-sm {{ $q->response_due_date && $q->response_due_date->isPast() && $q->status !== 'closed' ? 'text-red-600 font-black' : 'text-slate-600' }}">
                        {{ $q->response_due_date?->format('M j, Y') ?? '—' }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $q->statusBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $q->status)) }}</span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.staff-queries.show', $q) }}" class="text-sm font-black text-pink-600 hover:text-pink-800">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No queries found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($queries->hasPages())
        <div class="px-5 py-4 border-t border-slate-200">{{ $queries->links() }}</div>
        @endif
    </div>

</div>
@endsection
