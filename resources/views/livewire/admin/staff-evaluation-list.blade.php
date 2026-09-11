<div class="pb-card overflow-hidden">
    <div class="pb-table-wrapper border-0 rounded-none">
        <table class="pb-table pb-table--cards">
            <thead>
                <tr>
                    <th>Staff</th>
                    <th>Period</th>
                    <th>Overall</th>
                    <th>Avg</th>
                    <th>Evaluated By</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($evaluations as $ev)
                <tr wire:key="staff-evaluation-{{ $ev->id }}">
                    <td data-label="Staff">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $ev->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $ev->staff?->displayName() }}</p>
                                <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $ev->staff?->role ?? '')) }}</p>
                            </div>
                        </div>
                    </td>
                    <td data-label="Period" class="text-sm font-semibold text-slate-800">{{ $ev->periodLabel() }}</td>
                    <td data-label="Overall">
                        <span class="text-base">{{ $ev->ratingStars($ev->overall_rating) }}</span>
                    </td>
                    <td data-label="Avg" class="text-sm font-semibold text-slate-700">{{ number_format($ev->averageRating(), 1) }}/5</td>
                    <td data-label="Evaluated By" class="text-sm text-slate-600">{{ $ev->evaluatedBy?->displayName() }}</td>
                    <td data-label="Status">
                        <span class="pb-badge {{ $ev->status === 'acknowledged' ? 'pb-badge-success' : 'pb-badge-secondary' }}">{{ ucfirst($ev->status) }}</span>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.evaluations.show', $ev) }}" class="text-sm font-semibold text-slate-600 hover:text-brand-600">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="pb-empty"><p class="pb-empty-title">No evaluations found.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t border-slate-200 space-y-3">
        <p class="text-xs font-semibold text-slate-400">
            Showing {{ number_format($evaluations->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('evaluation', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-2" wire:poll.visible="loadMore">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more evaluations as you scroll...
                </span>
                <span class="text-xs font-semibold uppercase tracking-wide text-brand-600" wire:loading wire:target="loadMore">
                    Loading more evaluations...
                </span>
                <button type="button" wire:click="loadMore" class="pb-btn pb-btn-sm pb-btn-outline">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
