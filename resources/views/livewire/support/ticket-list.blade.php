<div>
    <div class="card bg-white rounded-2xl shadow-md border border-slate-100">
        <div class="card-body p-6">
            <p class="text-sm font-bold text-slate-400 mb-4">
                {{ number_format($this->totalResults) }} {{ Str::plural('ticket', $this->totalResults) }} found
            </p>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-slate-600 font-semibold">Ticket #</th>
                            <th class="text-slate-600 font-semibold">Subject</th>
                            <th class="text-slate-600 font-semibold hidden sm:table-cell">Category</th>
                            <th class="text-slate-600 font-semibold">Priority</th>
                            <th class="text-slate-600 font-semibold">Status</th>
                            <th class="text-slate-600 font-semibold hidden md:table-cell">Created</th>
                            <th class="text-slate-600 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                <td class="font-mono text-sm font-semibold text-slate-800">{{ $ticket->ticket_number }}</td>
                                <td>
                                    <p class="font-medium text-slate-800">{{ Str::limit($ticket->subject, 40) }}</p>
                                    <p class="text-xs text-slate-400 sm:hidden">{{ $ticket->created_at->format('M d, Y') }}</p>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="badge badge-sm bg-slate-100 text-slate-700 capitalize">{{ $ticket->category }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $ticket->getPriorityColor() }} badge-sm capitalize">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $ticket->getStatusColor() }} badge-sm capitalize">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                </td>
                                <td class="hidden md:table-cell text-sm text-slate-500">
                                    {{ $ticket->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('support.show', $ticket) }}"
                                       class="btn btn-xs btn-ghost text-pink-600 hover:bg-pink-50"
                                       title="View Ticket">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </td>
                             </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197"/>
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">No support tickets yet</p>
                                        <p class="text-sm text-slate-400 mt-1">Create your first support ticket</p>
                                        <a href="{{ route('support.create') }}" class="btn btn-sm btn-pink-600 text-white mt-4">
                                            New Ticket
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->isNotEmpty())
                @if($this->hasMore)
                    <div class="mt-6 flex flex-col items-center gap-3" wire:poll.visible="loadMore">
                        <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                            Loading more tickets as you scroll...
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                            Loading more tickets...
                        </span>
                        <button type="button" wire:click="loadMore" class="btn btn-outline border-slate-300 hover:border-pink-400 hover:text-pink-700 font-black">
                            Load More
                        </button>
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-center">
                        <p class="text-sm font-black text-slate-700">You have reached the end of your tickets.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
