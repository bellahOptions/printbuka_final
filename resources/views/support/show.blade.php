@extends('layouts.theme')

@section('title', 'Ticket #' . $ticket->ticket_number . ' | Printbuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('support.index') }}" class="hover:text-pink-600 transition flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Tickets
                </a>
                <span>/</span>
                <span class="text-slate-700 font-medium">{{ $ticket->ticket_number }}</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $ticket->subject }}</h1>
                    <p class="text-sm text-slate-500 mt-1">Created {{ $ticket->created_at->format('F j, Y \a\t g:i A') }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="badge badge-{{ $ticket->getPriorityColor() }} badge-lg capitalize">
                        {{ $ticket->priority }}
                    </span>
                    <span class="badge badge-{{ $ticket->getStatusColor() }} badge-lg capitalize">
                        {{ str_replace('_', ' ', $ticket->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Main Content - Messages --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Original Message --}}
                <div class="card bg-white rounded-2xl shadow-md border border-slate-100">
                    <div class="card-body p-6">
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 rounded-full bg-pink-100 flex items-center justify-center shrink-0">
                                <span class="text-pink-600 font-bold text-sm">
                                    {{ substr($ticket->user->first_name ?? 'U', 0, 1) }}{{ substr($ticket->user->last_name ?? '', 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $ticket->user->first_name ?? 'Customer' }} {{ $ticket->user->last_name ?? '' }}</p>
                                        <p class="text-xs text-slate-400">{{ $ticket->created_at->format('F j, Y \a\t g:i A') }}</p>
                                    </div>
                                    <div class="badge badge-sm bg-slate-100 text-slate-600">Original Ticket</div>
                                </div>
                                <div class="prose prose-sm max-w-none text-slate-700 whitespace-pre-wrap">
                                    {{ $ticket->message }}
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                        </svg>
                                        Category: {{ ucfirst($ticket->category) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Replies --}}
                @foreach ($replies as $reply)
                    <div class="card bg-white rounded-2xl shadow-md border border-slate-100 {{ $reply->is_staff_reply ? 'border-l-4 border-l-pink-500' : '' }}">
                        <div class="card-body p-6">
                            <div class="flex items-start gap-4">
                                <div class="h-10 w-10 rounded-full {{ $reply->is_staff_reply ? 'bg-pink-600' : 'bg-slate-200' }} flex items-center justify-center shrink-0">
                                    @if($reply->is_staff_reply)
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    @else
                                        <span class="text-slate-600 font-bold text-sm">
                                            {{ substr($reply->user->first_name ?? 'U', 0, 1) }}{{ substr($reply->user->last_name ?? '', 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                        <div>
                                            <p class="font-bold text-slate-900">
                                                @if($reply->is_staff_reply)
                                                    PrintBuka Support Team
                                                @else
                                                    {{ $reply->user->first_name ?? 'Customer' }} {{ $reply->user->last_name ?? '' }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-slate-400">{{ $reply->created_at->format('F j, Y \a\t g:i A') }}</p>
                                        </div>
                                        @if($reply->is_staff_reply)
                                            <div class="badge badge-sm bg-pink-100 text-pink-700">Staff Response</div>
                                        @endif
                                    </div>
                                    <div class="prose prose-sm max-w-none text-slate-700 whitespace-pre-wrap">
                                        {{ $reply->message }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Reply Form (if ticket is not closed) --}}
                @if($ticket->status !== 'closed')
                    <div class="card bg-white rounded-2xl shadow-md border border-slate-100">
                        <div class="card-body p-6">
                            <h3 class="text-lg font-bold text-slate-900 mb-4">Add a Reply</h3>
                            <form action="{{ route('support.reply', $ticket) }}" method="POST">
                                @csrf
                                <div class="form-control w-full">
                                    <textarea name="message" rows="5" 
                                        class="textarea textarea-bordered w-full focus:textarea-primary"
                                        placeholder="Type your reply here..."></textarea>
                                </div>
                                <div class="flex justify-end mt-4">
                                    <button type="submit" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert bg-slate-100 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-slate-600 text-sm">This ticket is closed. Please create a new ticket for further assistance.</span>
                    </div>
                @endif
            </div>

            {{-- Sidebar - Ticket Info --}}
            <div class="space-y-6">
                {{-- Ticket Details Card --}}
                <div class="card bg-white rounded-2xl shadow-md border border-slate-100">
                    <div class="card-body p-6">
                        <h3 class="font-bold text-slate-900 mb-4">Ticket Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase">Ticket Number</p>
                                <p class="text-sm font-mono font-semibold text-slate-800">{{ $ticket->ticket_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase">Created</p>
                                <p class="text-sm text-slate-700">{{ $ticket->created_at->format('F j, Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->created_at->format('g:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase">Last Updated</p>
                                <p class="text-sm text-slate-700">{{ $ticket->updated_at->diffForHumans() }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase">Category</p>
                                <p class="text-sm text-slate-700 capitalize">{{ $ticket->category }}</p>
                            </div>
                            @if($ticket->closed_at)
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase">Closed On</p>
                                    <p class="text-sm text-slate-700">{{ $ticket->closed_at->format('F j, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions Card --}}
                @if($ticket->status !== 'closed')
                    <div class="card bg-white rounded-2xl shadow-md border border-slate-100">
                        <div class="card-body p-6">
                            <h3 class="font-bold text-slate-900 mb-4">Actions</h3>
                            <form action="{{ route('support.close', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this ticket?');">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-outline btn-error w-full">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Close Ticket
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Need Help? Card --}}
                <div class="card bg-gradient-to-r from-pink-50 to-cyan-50 rounded-2xl border border-pink-100">
                    <div class="card-body p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197"/>
                            </svg>
                            <h3 class="font-bold text-slate-900">Need Urgent Help?</h3>
                        </div>
                        <p class="text-sm text-slate-600 mb-3">For urgent matters, please contact us directly:</p>
                        <div class="space-y-2">
                            <p class="text-sm flex items-center gap-2">
                                <svg class="h-4 w-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                +234 801 234 5678
                            </p>
                            <p class="text-sm flex items-center gap-2">
                                <svg class="h-4 w-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                support@printbuka.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection