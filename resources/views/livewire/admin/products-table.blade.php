<section class="mt-8 space-y-4">
    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif

    <div class="pb-card p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="pb-field w-full max-w-xl">
                <label class="pb-label">Search products</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Name, category, service type..."
                    class="pb-input w-full"
                >
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <div class="pb-field">
                    <label class="pb-label">Batch action</label>
                    <select wire:model.live="batchAction" class="pb-select">
                        <option value="">Select action</option>
                        <option value="activate">Set Active</option>
                        <option value="hide">Set Hidden</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                </div>
                <button
                    type="button"
                    wire:click="applyBatchAction"
                    class="pb-btn pb-btn-md pb-btn-ink"
                >
                    Apply
                </button>
            </div>
        </div>

        @error('batchAction')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
        @error('selected')
            <p class="pb-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="pb-table-wrapper">
        <table class="pb-table min-w-[980px]">
            <thead>
                <tr>
                    @php
                        $loadedIds = $products->pluck('id')->map(fn ($id): int => (int) $id)->all();
                        $allLoadedSelected = $loadedIds !== [] && count(array_diff($loadedIds, $selected)) === 0;
                    @endphp
                    <th>
                        <input type="checkbox" wire:click="toggleSelectLoadedSelection" class="h-4 w-4 rounded border-slate-300 text-pink-600" @checked($allLoadedSelected)>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('name')" class="inline-flex items-center gap-1">
                            Name
                            @if ($sortField === 'name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>Category</th>
                    <th>
                        <button type="button" wire:click="sortBy('moq')" class="inline-flex items-center gap-1">
                            MOQ
                            @if ($sortField === 'moq')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('price')" class="inline-flex items-center gap-1">
                            Price
                            @if ($sortField === 'price')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th>
                        <button type="button" wire:click="sortBy('is_active')" class="inline-flex items-center gap-1">
                            Status
                            @if ($sortField === 'is_active')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="product-row-{{ $product->id }}">
                        <td>
                            <input type="checkbox" value="{{ $product->id }}" wire:model.live="selected" class="h-4 w-4 rounded border-slate-300 text-pink-600">
                        </td>
                        <td class="font-black text-slate-900">{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? 'Unassigned' }}</td>
                        <td>{{ $product->moq }}</td>
                        <td>
                            @if ($product->hasAvailablePrice())
                                NGN {{ number_format((float) $product->price, 2) }}
                            @else
                                <span class="pb-badge pb-badge-warning">Quote only</span>
                            @endif
                        </td>
                        <td>{{ $product->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}" class="pb-btn pb-btn-sm pb-btn-ghost">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="pb-btn pb-btn-sm pb-btn-ghost text-red-500 hover:text-red-600 hover:bg-red-50">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="pb-empty">No products matched your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs font-bold text-slate-400">
        Showing {{ number_format($products->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('product', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more products as you scroll...
            </span>
            <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more products...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</section>
