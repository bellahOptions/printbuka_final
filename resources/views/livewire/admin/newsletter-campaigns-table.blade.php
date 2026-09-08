<div>
    <div class="overflow-x-auto rounded-xl border border-slate-100">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">Subject</th>
                    <th class="px-4 py-3">Sent By</th>
                    <th class="px-4 py-3">Recipients</th>
                    <th class="px-4 py-3">Delivered</th>
                    <th class="px-4 py-3">Failed</th>
                    <th class="px-4 py-3">Sent At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($campaigns as $campaign)
                    <tr wire:key="newsletter-campaign-row-{{ $campaign->id }}">
                        <td class="px-4 py-3">
                            <p class="font-black text-slate-900">{{ $campaign->subject }}</p>
                            @if ($campaign->preheader)
                                <p class="text-xs font-semibold text-slate-500">{{ $campaign->preheader }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-700">{{ $campaign->sender?->displayName() ?? 'System' }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-700">{{ number_format($campaign->recipient_count) }}</td>
                        <td class="px-4 py-3 font-semibold text-emerald-700">{{ number_format($campaign->emails_sent) }}</td>
                        <td class="px-4 py-3 font-semibold text-pink-700">{{ number_format($campaign->emails_failed) }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-600">{{ $campaign->sent_at?->format('M j, Y g:i A') ?? 'Pending' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">
                            No newsletter campaign has been sent yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs font-bold text-slate-400">
        Showing {{ number_format($campaigns->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('campaign', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more campaigns as you scroll...
            </span>
            <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more campaigns...
            </span>
            <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                Load More
            </button>
        </div>
    @endif
</div>
