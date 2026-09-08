<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="font-black text-slate-500">Ticket #</th>
                    <th class="font-black text-slate-500">Subject</th>
                    <th class="font-black text-slate-500">Priority</th>
                    <th class="font-black text-slate-500">Status</th>
                    <th class="font-black text-slate-500">Assigned To</th>
                    <th class="font-black text-slate-500">Updated</th>
                    <th class="font-black text-slate-500 text-right">Manage</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr wire:key="ticket-row-{{ $ticket->id }}">
                        <td class="font-mono text-xs font-black text-slate-700">{{ $ticket->ticket_number }}</td>
                        <td>
                            <p class="font-bold text-slate-900">{{ Str::limit($ticket->subject, 50) }}</p>
                            <p class="text-xs font-semibold text-slate-400">{{ ucfirst($ticket->category) }}</p>
                        </td>
                        <td>
                            <span class="badge badge-{{ $ticket->getPriorityColor() }} badge-sm capitalize">{{ $ticket->priority }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $ticket->getStatusColor() }} badge-sm capitalize">{{ str_replace('_', ' ', $ticket->status) }}</span>
                        </td>
                        <td>
                            <p class="text-sm font-semibold text-slate-700">
                                {{ $ticket->assignedStaff?->displayName() ?? 'Unassigned' }}
                            </p>
                        </td>
                        <td class="text-sm font-semibold text-slate-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-xs btn-outline border-slate-200 hover:border-pink-400 hover:text-pink-700 font-black">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-sm font-semibold text-slate-500">No tickets yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-6 py-4 space-y-3">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($tickets->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('ticket', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-2" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more tickets as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more tickets...
                </span>
                <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
