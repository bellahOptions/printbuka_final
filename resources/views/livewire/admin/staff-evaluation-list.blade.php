<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="border-b border-slate-200 bg-slate-50">
            <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                <th class="px-5 py-3.5 text-left">Staff</th>
                <th class="px-5 py-3.5 text-left">Period</th>
                <th class="px-5 py-3.5 text-left">Overall</th>
                <th class="px-5 py-3.5 text-left">Avg</th>
                <th class="px-5 py-3.5 text-left">Evaluated By</th>
                <th class="px-5 py-3.5 text-left">Status</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($evaluations as $ev)
            <tr wire:key="staff-evaluation-{{ $ev->id }}" class="hover:bg-slate-50/70 transition">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ $ev->staff?->profilePhotoUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $ev->staff?->displayName() }}</p>
                            <p class="text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $ev->staff?->role ?? '')) }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $ev->periodLabel() }}</td>
                <td class="px-5 py-4">
                    <span class="text-base">{{ $ev->ratingStars($ev->overall_rating) }}</span>
                </td>
                <td class="px-5 py-4 text-sm font-semibold text-slate-700">{{ number_format($ev->averageRating(), 1) }}/5</td>
                <td class="px-5 py-4 text-sm text-slate-600">{{ $ev->evaluatedBy?->displayName() }}</td>
                <td class="px-5 py-4">
                    <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $ev->status === 'acknowledged' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($ev->status) }}</span>
                </td>
                <td class="px-5 py-4 text-right">
                    <a href="{{ route('admin.evaluations.show', $ev) }}" class="text-sm font-black text-slate-600 hover:text-pink-600">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No evaluations found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-5 py-4 border-t border-slate-200 space-y-3">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($evaluations->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('evaluation', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-2" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more evaluations as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more evaluations...
                </span>
                <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
