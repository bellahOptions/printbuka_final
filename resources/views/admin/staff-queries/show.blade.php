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

    {{-- Conversation: staff response + follow-up comments, in one reply thread --}}
    <div class="pb-card p-6" id="comments">
        <h2 class="pb-section-title mb-4">Conversation</h2>

        <div class="space-y-4 {{ $thread->isNotEmpty() ? 'mb-5' : '' }}">
            @forelse ($thread as $item)
                <div class="flex gap-3">
                    <img src="{{ $item['author']?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover shrink-0" alt="">
                    <div @class([
                        'flex-1 rounded-xl border p-3',
                        'bg-slate-50 border-slate-200' => $item['is_staff'],
                        'bg-pink-50 border-pink-200' => ! $item['is_staff'],
                    ])>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <p class="text-sm font-black text-slate-900">{{ $item['author']?->displayName() }}</p>
                            @if ($item['is_staff'])
                                <span class="pb-badge bg-slate-200 text-slate-700 text-[10px]">Staff Response</span>
                            @endif
                            @if ($isHr && ! $item['is_staff'] && ! $item['visible_to_staff'])
                                <span class="pb-badge bg-amber-100 text-amber-800 text-[10px]">Internal only</span>
                            @endif
                            <span class="text-xs text-slate-400">{{ $item['at']?->format('M j, Y g:i A') }}</span>
                        </div>
                        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line ql-editor">{!! $item['is_staff'] ? $item['body'] : e($item['body']) !!}</div>
                    </div>
                </div>
            @empty
                <p class="text-sm font-semibold text-slate-400 text-center py-4">No responses or comments yet.</p>
            @endforelse
        </div>

        @if ($isHr)
            <form method="POST" action="{{ route('admin.staff-queries.comments', $query) }}">
                @csrf
                <textarea name="comment" rows="3" required placeholder="Write a follow-up comment..." class="pb-textarea w-full mb-3"></textarea>
                <label class="flex items-center gap-2 text-sm text-slate-600 mb-3">
                    <input type="checkbox" name="visible_to_staff" value="1" class="checkbox checkbox-sm">
                    Share this comment with {{ $query->staff?->displayName() }}
                </label>
                <button type="submit" class="pb-btn pb-btn-outline">Post Comment</button>
            </form>
        @elseif ($isSelf && ! $query->staff_response && in_array($query->status, ['pending', 'awaiting_response']))
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm font-black text-amber-900 mb-1">Your Response Required</p>
                <p class="text-xs text-amber-700 mb-3">Please provide your formal response to this query.</p>
                <form method="POST" action="{{ route('admin.staff-queries.respond', $query) }}">
                    @csrf
                    <textarea name="staff_response" rows="5" required data-rich-editor placeholder="Write your formal response here..." class="pb-textarea w-full mb-3"></textarea>
                    <button type="submit" class="pb-btn pb-btn-primary">Submit Response</button>
                </form>
            </div>
        @endif
    </div>

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

</div>
@endsection
