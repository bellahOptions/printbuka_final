@extends('layouts.admin')

@section('title', 'Ticket #'.$ticket->ticket_number.' | Printbuka')

@section('content')
<main class="mx-auto max-w-6xl">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('admin.support.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-slate-600 hover:text-pink-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Tickets
            </a>
            <h1 class="mt-3 text-2xl font-black text-slate-950">{{ $ticket->subject }}</h1>
            <p class="text-sm font-semibold text-slate-500">Ticket #{{ $ticket->ticket_number }} created {{ $ticket->created_at->format('F j, Y g:i A') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="badge badge-{{ $ticket->getPriorityColor() }} badge-lg capitalize">{{ $ticket->priority }}</span>
            <span class="badge badge-{{ $ticket->getStatusColor() }} badge-lg capitalize">{{ str_replace('_', ' ', $ticket->status) }}</span>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <div class="space-y-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Original Ticket</p>
                <p class="mt-3 whitespace-pre-wrap text-sm font-semibold leading-7 text-slate-700">{{ $ticket->message }}</p>
            </article>

            @foreach($replies as $reply)
                <article class="rounded-2xl border bg-white p-6 shadow-sm {{ $reply->is_staff_reply ? 'border-pink-200' : 'border-slate-200' }}">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-black text-slate-900">{{ $reply->user?->displayName() ?? 'Staff' }}</p>
                        <p class="text-xs font-semibold text-slate-400">{{ $reply->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <p class="mt-3 whitespace-pre-wrap text-sm font-semibold leading-7 text-slate-700">{{ $reply->message }}</p>
                </article>
            @endforeach

            @if($ticket->status !== 'closed')
                <form action="{{ route('admin.support.reply', $ticket) }}" method="POST" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    <label class="label"><span class="label-text font-black text-slate-700">Add Reply</span></label>
                    <textarea name="message" rows="5" class="textarea textarea-bordered border-slate-200 w-full @error('message') textarea-error @enderror" required>{{ old('message') }}</textarea>
                    @error('message') <span class="text-xs text-pink-600 mt-1">{{ $message }}</span> @enderror
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Send Reply</button>
                    </div>
                </form>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Assigned To</p>
                <p class="mt-2 text-sm font-black text-slate-900">{{ $ticket->assignedStaff?->displayName() ?? 'Unassigned' }}</p>
                <p class="mt-4 text-xs font-black uppercase tracking-wide text-slate-400">Category</p>
                <p class="mt-2 text-sm font-bold text-slate-700">{{ ucfirst($ticket->category) }}</p>
            </div>

            @if($ticket->status !== 'closed')
                <form action="{{ route('admin.support.close', $ticket) }}" method="POST" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" onsubmit="return confirm('Close this ticket?');">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-outline btn-error w-full font-black">Close Ticket</button>
                </form>
            @endif
        </aside>
    </div>
</main>
@endsection
