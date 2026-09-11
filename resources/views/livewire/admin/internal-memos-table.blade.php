<div class="pb-table-wrapper">
    <table class="pb-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Sent</th>
                <th>Delivery</th>
                <th>By</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($memos as $memo)
            <tr wire:key="internal-memo-row-{{ $memo->id }}">
                <td class="font-black text-slate-900">{{ $memo->subject }}</td>
                <td class="text-sm text-slate-600">{{ $memo->sent_at?->format('M j, Y g:i A') ?? '—' }}</td>
                <td class="text-sm text-slate-600">{{ $memo->emails_sent }} sent{{ $memo->emails_failed > 0 ? ', '.$memo->emails_failed.' failed' : '' }} / {{ $memo->recipient_count }}</td>
                <td class="text-sm text-slate-600">{{ $memo->sentBy?->displayName() }}</td>
                <td class="text-right">
                    <a href="{{ route('admin.memos.show', $memo) }}" class="text-sm font-black text-slate-700 hover:text-pink-600">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-12 text-center text-sm text-slate-400 font-semibold">No memos sent yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-5 py-4 border-t border-slate-200">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($memos->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('memo', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more memos as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more memos...
                </span>
                <button type="button" wire:click="loadMore" class="pb-btn pb-btn-outline">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
