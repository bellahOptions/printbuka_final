<div>
    <div class="pb-table-wrapper">
        <table class="pb-table pb-table--cards w-full md:min-w-[900px]">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Sent By</th>
                    <th>Recipients</th>
                    <th>Delivered</th>
                    <th>Failed</th>
                    <th>Sent At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($campaigns as $campaign)
                    <tr wire:key="newsletter-campaign-row-{{ $campaign->id }}">
                        <td data-label="Subject">
                            <p class="font-semibold text-slate-900">{{ $campaign->subject }}</p>
                            @if ($campaign->preheader)
                                <p class="text-xs text-slate-500">{{ $campaign->preheader }}</p>
                            @endif
                        </td>
                        <td data-label="Sent By" class="font-medium text-slate-700">{{ $campaign->sender?->displayName() ?? 'System' }}</td>
                        <td data-label="Recipients" class="font-medium text-slate-700">{{ number_format($campaign->recipient_count) }}</td>
                        <td data-label="Delivered" class="font-medium text-emerald-700">{{ number_format($campaign->emails_sent) }}</td>
                        <td data-label="Failed" class="font-medium text-brand-700">{{ number_format($campaign->emails_failed) }}</td>
                        <td data-label="Sent At" class="font-medium text-slate-600">{{ $campaign->sent_at?->format('M j, Y g:i A') ?? 'Pending' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="pb-empty">
                                <p class="pb-empty-title">No newsletter campaign has been sent yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs font-medium text-slate-400">
        Showing {{ number_format($campaigns->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('campaign', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more campaigns as you scroll...
            </span>
            <span class="text-xs font-semibold uppercase tracking-wide text-brand-600" wire:loading wire:target="loadMore">
                Loading more campaigns...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</div>
