<div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/80">
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Date</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Type</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Category</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Description</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500 text-right">Amount</th>
                    <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($entries as $entry)
                    <tr wire:key="finance-entry-row-{{ $entry->id }}" class="group transition-all duration-200 hover:bg-slate-50/80">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-900">{{ $entry->entry_date->format('M j, Y') }}</p>
                            <p class="text-xs text-slate-500">{{ $entry->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-black uppercase tracking-wider {{ $entry->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-pink-100 text-pink-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $entry->type === 'income' ? 'bg-emerald-500' : 'bg-pink-500' }}"></span>
                                {{ ucfirst($entry->type) }}
                                @if($entry->entry_type === 'credit_from_ceo')
                                    <span class="ml-1 text-[9px]">(CEO Credit)</span>
                                @endif
                            </span>
                            @if($entry->type === 'income' && $entry->isRefunded())
                                <span class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-black uppercase tracking-wider text-red-700">
                                    Refunded
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-700">{{ $entry->category }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-900">{{ $entry->description }}</p>
                            @if($entry->reference)
                                <p class="text-xs text-slate-500">Ref: {{ $entry->reference }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <p class="font-black {{ $entry->type === 'income' ? 'text-emerald-700' : 'text-pink-700' }}">
                                {{ $entry->type === 'income' ? '+' : '-' }} ₦{{ number_format((float) $entry->amount, 2) }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.finance.show', $entry) }}" class="rounded-lg p-2 text-slate-700 transition-all duration-200 hover:bg-slate-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.finance.download', $entry) }}" class="rounded-lg p-2 text-slate-700 transition-all duration-200 hover:bg-slate-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </a>
                                @if($entry->type === 'income')
                                    <span class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-black uppercase tracking-wider text-emerald-700">Auto</span>
                                @elseif(auth()->user()?->canAdmin('finance.view'))
                                    <a href="{{ route('admin.finance.edit', $entry) }}" class="rounded-lg p-2 text-pink-700 transition-all duration-200 hover:bg-pink-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.finance.destroy', $entry) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg p-2 text-slate-500 transition-all duration-200 hover:bg-red-50 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="rounded-full bg-slate-100 p-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-500">No finance entries yet.</p>
                                @if (auth()->user()?->canAdmin('finance.view'))
                                    <a href="{{ route('admin.finance.create') }}" class="text-sm font-bold text-pink-700 hover:text-pink-800">Add your first entry →</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200 space-y-3">
        <p class="text-xs font-bold text-slate-400">
            Showing {{ number_format($entries->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('entry', $totalCount) }}
        </p>

        @if ($hasMore)
            <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more entries as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more entries...
                </span>
                <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>
