<div class="pb-table-wrapper">
    <table class="pb-table">
        <thead>
            <tr>
                <th>Ref</th>
                <th>Staff</th>
                <th>Subject</th>
                <th>Type</th>
                <th>Date</th>
                <th>Due</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($queries as $q)
            <tr wire:key="staff-query-{{ $q->id }}">
                <td>
                    <span class="font-mono text-xs font-black text-pink-700">{{ $q->query_number }}</span>
                </td>
                <td>
                    <div class="flex items-center gap-2.5">
                        <img src="{{ $q->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $q->staff?->displayName() }}</p>
                            <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $q->staff?->role ?? '')) }}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <p class="text-sm font-semibold text-slate-800">{{ Str::limit($q->subject, 40) }}</p>
                </td>
                <td>
                    <span class="text-sm text-slate-600">{{ $q->typeLabel() }}</span>
                </td>
                <td class="text-sm text-slate-600">{{ $q->query_date->format('M j, Y') }}</td>
                <td class="text-sm {{ $q->response_due_date && $q->response_due_date->isPast() && $q->status !== 'closed' ? 'text-red-600 font-black' : 'text-slate-600' }}">
                    {{ $q->response_due_date?->format('M j, Y') ?? '—' }}
                </td>
                <td>
                    <span class="pb-badge {{ $q->statusBadgeClass() }}">{{ ucwords(str_replace('_', ' ', $q->status)) }}</span>
                </td>
                <td class="text-right">
                    <a href="{{ route('admin.staff-queries.show', $q) }}" class="text-sm font-black text-pink-600 hover:text-pink-800">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-12 text-center text-sm text-slate-400 font-semibold">No queries found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-5 py-4 border-t border-slate-200 space-y-3">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($queries->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('query', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-2" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more queries as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more queries...
                </span>
                <button type="button" wire:click="loadMore" class="pb-btn pb-btn-outline">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
