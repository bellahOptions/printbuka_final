<div class="space-y-3">
    @forelse ($advertisements as $ad)
        <article wire:key="advertisement-row-{{ $ad->id }}" class="pb-card p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="font-semibold text-slate-900">{{ $ad->title }}</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $placements[$ad->placement] ?? $ad->placement }} · {{ $ad->is_active ? 'Active' : 'Inactive' }}</p>
                    @if ($ad->body)
                        <p class="mt-2 text-sm text-slate-700">{{ $ad->body }}</p>
                    @endif
                </div>
                <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="pb-btn pb-btn-sm pb-btn-outline text-xs">Delete</button>
                </form>
            </div>
        </article>
    @empty
        <div class="pb-empty">
            <p class="pb-empty-title">No ads created yet.</p>
        </div>
    @endforelse

    <p class="text-xs font-medium text-slate-400">
        Showing {{ number_format($advertisements->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('ad', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more ads as you scroll...
            </span>
            <span class="text-xs font-semibold uppercase tracking-wide text-brand-600" wire:loading wire:target="loadMore">
                Loading more ads...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</div>
