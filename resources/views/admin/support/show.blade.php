@extends('layouts.admin')

@section('title', 'Ticket #'.$ticket->ticket_number.' | Printbuka')

@section('content')
<main class="mx-auto max-w-6xl">
    <div class="pb-page-header items-start">
        <div>
            <a href="{{ route('admin.support.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-slate-600 hover:text-pink-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Tickets
            </a>
            <h1 class="pb-page-title mt-3">{{ $ticket->subject }}</h1>
            <p class="pb-page-subtitle">Ticket #{{ $ticket->ticket_number }} created {{ $ticket->created_at->format('F j, Y g:i A') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="pb-badge {{ match ($ticket->getPriorityColor()) { 'error' => 'pb-badge-danger', 'warning' => 'pb-badge-warning', 'success' => 'pb-badge-success', default => 'pb-badge-info' } }} capitalize">{{ $ticket->priority }}</span>
            <span class="pb-badge {{ match ($ticket->getStatusColor()) { 'error' => 'pb-badge-danger', 'warning' => 'pb-badge-warning', 'success' => 'pb-badge-success', default => 'pb-badge-info' } }} capitalize">{{ str_replace('_', ' ', $ticket->status) }}</span>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <div class="space-y-4">
            <article class="pb-card p-6">
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Original Ticket</p>
                <p class="mt-3 whitespace-pre-wrap text-sm font-semibold leading-7 text-slate-700">{{ $ticket->message }}</p>
            </article>

            @foreach($replies as $reply)
                <article class="pb-card p-6 {{ $reply->is_staff_reply ? 'border-pink-200' : '' }}">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-black text-slate-900">{{ $reply->user?->displayName() ?? 'Staff' }}</p>
                        <p class="text-xs font-semibold text-slate-400">{{ $reply->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <p class="mt-3 whitespace-pre-wrap text-sm font-semibold leading-7 text-slate-700">{{ $reply->message }}</p>
                </article>
            @endforeach

            @if($ticket->status !== 'closed')
                <form action="{{ route('admin.support.reply', $ticket) }}" method="POST" class="pb-card p-6">
                    @csrf
                    <div class="pb-field">
                        <label class="pb-label">Add Reply</label>
                        <textarea name="message" rows="5" data-rich-editor class="pb-textarea w-full {{ $errors->has('message') ? 'pb-input-error' : '' }}" required>{{ old('message') }}</textarea>
                        @error('message') <p class="pb-field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="pb-btn pb-btn-primary">Send Reply</button>
                    </div>
                </form>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="pb-card p-5">
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Assigned To</p>
                <p class="mt-2 text-sm font-black text-slate-900">{{ $ticket->assignedStaff?->displayName() ?? 'Unassigned' }}</p>
                <p class="mt-4 text-xs font-black uppercase tracking-wide text-slate-400">Category</p>
                <p class="mt-2 text-sm font-bold text-slate-700">{{ ucfirst($ticket->category) }}</p>
            </div>

            @if($ticket->status !== 'closed')
                <form action="{{ route('admin.support.close', $ticket) }}" method="POST" class="pb-card p-5" onsubmit="return confirm('Close this ticket?');">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="pb-btn pb-btn-destructive w-full">Close Ticket</button>
                </form>
            @endif
        </aside>
    </div>
</main>
@endsection
