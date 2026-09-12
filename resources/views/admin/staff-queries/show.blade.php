@extends('layouts.admin')
@section('title', $query->query_number.' | Staff Query | Printbuka')

@section('content')
@php($viewer = auth()->user())
@php($isHr = $viewer->canAdmin('staff.queries') || $viewer->canAdmin('*'))
@php($isSelf = $viewer->id === $query->staff_id)

<div class="mx-auto max-w-3xl space-y-6">

    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.staff-queries.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Queries</a>
            <div class="mt-3 flex items-center gap-4">
                <h1 class="pb-page-title">{{ $query->query_number }}</h1>
                <span class="pb-badge {{ $query->statusBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $query->status)) }}</span>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="pb-alert pb-alert-error">
            @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    {{-- Query Details --}}
    <div class="pb-card p-6">
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
            <div class="rounded-xl bg-pink-50 border border-pink-200 text-sm text-slate-800 leading-relaxed ql-editor">{!! $query->description !!}</div>
        </div>

        @if ($isHr)
        <div class="mt-5 flex flex-wrap items-end justify-between gap-4 border-t border-slate-100 pt-4">
            <div class="space-y-1 text-xs text-slate-500">
                @if ($query->cc_emails)
                    <p><span class="font-black uppercase tracking-wide text-slate-400">CC:</span> {{ implode(', ', $query->ccList()) }}</p>
                @endif
                @if ($query->bcc_emails)
                    <p><span class="font-black uppercase tracking-wide text-slate-400">BCC:</span> {{ implode(', ', $query->bccList()) }}</p>
                @endif
                <p>
                    @if ($query->email_last_sent_at)
                        Last emailed {{ $query->email_last_sent_at->diffForHumans() }}
                        @if ($query->email_send_count > 1)
                            ({{ $query->email_send_count }}x)
                        @endif
                    @else
                        Not yet emailed.
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('admin.staff-queries.resend', $query) }}">
                @csrf
                <button type="submit" class="pb-btn pb-btn-outline text-xs">Resend Query Email</button>
            </form>
        </div>
        @endif
    </div>

    {{-- Staff Response --}}
    @if ($query->staff_response)
    <div class="pb-card p-6">
        <div class="flex items-center gap-3 mb-4">
            <h2 class="pb-section-title">Staff Response</h2>
            <span class="text-xs text-slate-500">{{ $query->staff_responded_at?->format('M j, Y g:i A') }}</span>
        </div>
        <div class="rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 leading-relaxed ql-editor">{!! $query->staff_response !!}</div>
    </div>
    @elseif ($isSelf && in_array($query->status, ['pending', 'awaiting_response']))
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
        <h2 class="text-base font-black text-amber-900 mb-1">Your Response Required</h2>
        <p class="text-sm text-amber-700 mb-4">Please provide your formal response to this query.</p>
        <form method="POST" action="{{ route('admin.staff-queries.respond', $query) }}">
            @csrf
            <textarea name="staff_response" rows="5" required data-rich-editor placeholder="Write your formal response here..." class="pb-textarea w-full mb-3"></textarea>
            <button type="submit" class="pb-btn pb-btn-primary">Submit Response</button>
        </form>
    </div>
    @else
    <div class="pb-card p-5">
        <p class="text-sm font-semibold text-slate-400 text-center">No response submitted yet.</p>
    </div>
    @endif

    {{-- Resolution --}}
    @if ($query->status === 'closed')
    <div class="pb-card p-6">
        <h2 class="pb-section-title mb-3">Resolution</h2>
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
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-slate-800 leading-relaxed ql-editor">{!! $query->resolution_notes !!}</div>
        </div>
        @endif
    </div>
    @elseif ($isHr && $query->staff_response)
    <div class="pb-card p-6">
        <h2 class="pb-section-title mb-4">Close Query</h2>
        <form method="POST" action="{{ route('admin.staff-queries.close', $query) }}">
            @csrf
            <textarea name="resolution_notes" rows="3" data-rich-editor placeholder="Resolution notes (optional)..." class="pb-textarea w-full mb-3"></textarea>
            <button type="submit" class="pb-btn pb-btn-ink">Close Query</button>
        </form>
    </div>
    @endif

    {{-- Internal Comments (HR / MD / Super Admin only) --}}
    @if ($isHr)
    <div class="pb-card p-6" id="comments">
        <h2 class="pb-section-title mb-4">Internal Comments</h2>
        <p class="text-xs text-slate-400 mb-4">Visible only to HR, MD/CEO, and Super Admin — not shown to {{ $query->staff?->displayName() }}.</p>

        <div class="space-y-4 mb-5">
            @forelse ($query->comments as $comment)
                <div class="flex gap-3">
                    <img src="{{ $comment->user?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover shrink-0" alt="">
                    <div class="flex-1 rounded-xl bg-slate-50 border border-slate-200 p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-sm font-black text-slate-900">{{ $comment->user?->displayName() }}</p>
                            <span class="text-xs text-slate-400">{{ $comment->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $comment->comment }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm font-semibold text-slate-400 text-center py-4">No internal comments yet.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('admin.staff-queries.comments', $query) }}">
            @csrf
            <textarea name="comment" rows="3" required placeholder="Add an internal comment..." class="pb-textarea w-full mb-3"></textarea>
            <button type="submit" class="pb-btn pb-btn-outline">Post Comment</button>
        </form>
    </div>
    @endif

</div>
@endsection
