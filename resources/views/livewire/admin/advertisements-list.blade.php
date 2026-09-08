<div class="space-y-3">
    @forelse ($advertisements as $ad)
        <article wire:key="advertisement-row-{{ $ad->id }}" class="rounded-md border border-slate-200 p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="font-black text-slate-950">{{ $ad->title }}</p>
                    <p class="mt-1 text-xs font-black uppercase tracking-wide text-slate-500">{{ $placements[$ad->placement] ?? $ad->placement }} · {{ $ad->is_active ? 'Active' : 'Inactive' }}</p>
                    @if ($ad->body)
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $ad->body }}</p>
                    @endif
                </div>
                <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-md border border-slate-200 px-3 py-2 text-xs font-black text-slate-600 transition hover:border-pink-300 hover:text-pink-700">Delete</button>
                </form>
            </div>
        </article>
    @empty
        <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm font-semibold text-slate-500">No ads created yet.</p>
    @endforelse

    <p class="text-xs font-bold text-slate-400">
        Showing {{ number_format($advertisements->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('ad', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more ads as you scroll...
            </span>
            <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more ads...
            </span>
            <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                Load More
            </button>
        </div>
    @endif
</div>
