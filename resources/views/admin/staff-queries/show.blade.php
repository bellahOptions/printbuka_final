@extends('layouts.admin')
@section('title', $query->query_number.' | Staff Query | Printbuka')

@section('content')
@php($viewer = auth()->user())
@php($isHr = $viewer->canAdmin('staff.queries') || $viewer->canAdmin('*'))
@php($isSelf = $viewer->id === $query->staff_id)

<div class="mx-auto max-w-3xl space-y-6">

    <div>
        <a href="{{ route('admin.staff-queries.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Queries</a>
        <div class="mt-3 flex items-center gap-4">
            <h1 class="text-2xl font-black text-slate-950">{{ $query->query_number }}</h1>
            <span class="rounded-full px-3 py-1 text-xs font-black {{ $query->statusBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $query->status)) }}</span>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
            @foreach ($errors->all() as $e)<p class="text-sm font-semibold text-pink-700">{{ $e }}</p>@endforeach
        </div>
    @endif

    {{-- Query Details --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-x-8 gap-y-4 sm:grid-cols-2">
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Staff Member</p>
                <div class="flex items-center gap-2 mt-1">
                    <img src="{{ $query->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                    <div>
                        <p class="text-sm font-black text-slate-900">{{ $query->staff?->displayName() }}</p>
                        <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $query->staff?->role ?? '')) }}</p>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Issued By</p>
                <p class="text-sm font-semibold text-slate-800 mt-1">{{ $query->issuedBy?->displayName() }}</p>
                <p class="text-xs text-slate-500">{{ $query->query_date->format('F j, Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Query Type</p>
                <p class="text-sm font-semibold text-slate-800 mt-1">{{ $query->typeLabel() }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Response Due</p>
                <p class="text-sm font-semibold mt-1 {{ $query->response_due_date && $query->response_due_date->isPast() && $query->status !== 'closed' ? 'text-red-600' : 'text-slate-800' }}">
                    {{ $query->response_due_date?->format('F j, Y') ?? 'No deadline set' }}
                </p>
            </div>
        </div>

        <div class="mt-5">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Subject</p>
            <p class="text-base font-black text-slate-950 mt-1">{{ $query->subject }}</p>
        </div>

        <div class="mt-4">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-2">Query Description</p>
            <div class="rounded-xl bg-pink-50 border border-pink-200 p-4 text-sm text-slate-800 leading-relaxed">{{ $query->description }}</div>
        </div>
    </div>

    {{-- Staff Response --}}
    @if ($query->staff_response)
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <h2 class="text-base font-black text-slate-950">Staff Response</h2>
            <span class="text-xs text-slate-500">{{ $query->staff_responded_at?->format('M j, Y g:i A') }}</span>
        </div>
        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-800 leading-relaxed">{{ $query->staff_response }}</div>
    </div>
    @elseif ($isSelf && in_array($query->status, ['pending', 'awaiting_response']))
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
        <h2 class="text-base font-black text-amber-900 mb-1">Your Response Required</h2>
        <p class="text-sm text-amber-700 mb-4">Please provide your formal response to this query.</p>
        <form method="POST" action="{{ route('admin.staff-queries.respond', $query) }}">
            @csrf
            <textarea name="staff_response" rows="5" required placeholder="Write your formal response here..." class="w-full rounded-xl border border-amber-300 px-4 py-3 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100 focus:outline-none mb-3"></textarea>
            <button type="submit" class="rounded-xl bg-amber-600 px-6 py-2.5 text-sm font-black text-white hover:bg-amber-700">Submit Response</button>
        </form>
    </div>
    @else
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
        <p class="text-sm font-semibold text-slate-400 text-center">No response submitted yet.</p>
    </div>
    @endif

    {{-- Resolution --}}
    @if ($query->status === 'closed')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-black text-slate-950 mb-3">Resolution</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Resolved By</p>
                <p class="text-sm font-semibold text-slate-800 mt-1">{{ $query->resolvedBy?->displayName() }}</p>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Resolved On</p>
                <p class="text-sm font-semibold text-slate-800 mt-1">{{ $query->resolved_at?->format('F j, Y') }}</p>
            </div>
        </div>
        @if ($query->resolution_notes)
        <div class="mt-3">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-2">Resolution Notes</p>
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-slate-800 leading-relaxed">{{ $query->resolution_notes }}</div>
        </div>
        @endif
    </div>
    @elseif ($isHr && $query->staff_response)
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-black text-slate-950 mb-4">Close Query</h2>
        <form method="POST" action="{{ route('admin.staff-queries.close', $query) }}">
            @csrf
            <textarea name="resolution_notes" rows="3" placeholder="Resolution notes (optional)..." class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none mb-3"></textarea>
            <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-black text-white hover:bg-slate-700">Close Query</button>
        </form>
    </div>
    @endif

</div>
@endsection
