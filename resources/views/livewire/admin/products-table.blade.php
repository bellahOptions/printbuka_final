<section class="mt-8 space-y-4">
    @if (session('status'))
        <p class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
    @endif

    <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <label class="w-full max-w-xl">
                <span class="text-xs font-black uppercase tracking-wide text-slate-500">Search products</span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Name, category, service type..."
                    class="mt-2 h-11 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"
                >
            </label>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <label>
                    <span class="text-xs font-black uppercase tracking-wide text-slate-500">Batch action</span>
                    <select wire:model.live="batchAction" class="mt-2 h-11 rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        <option value="">Select action</option>
                        <option value="activate">Set Active</option>
                        <option value="hide">Set Hidden</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                </label>
                <button
                    type="button"
                    wire:click="applyBatchAction"
                    class="h-11 rounded-md bg-slate-900 px-4 text-sm font-black text-white transition hover:bg-pink-700"
                >
                    Apply
                </button>
            </div>
        </div>

        @error('batchAction')
            <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
        @error('selected')
            <p class="mt-2 text-xs font-semibold text-pink-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[980px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    @php
                        $pageIds = $products->pluck('id')->map(fn ($id): int => (int) $id)->all();
                        $allPageSelected = $pageIds !== [] && count(array_diff($pageIds, $selected)) === 0;
                    @endphp
                    <th class="px-4 py-4">
                        <input type="checkbox" wire:click="toggleSelectPageSelection" class="h-4 w-4 rounded border-slate-300 text-pink-600" @checked($allPageSelected)>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('name')" class="inline-flex items-center gap-1">
                            Name
                            @if ($sortField === 'name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">Category</th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('moq')" class="inline-flex items-center gap-1">
                            MOQ
                            @if ($sortField === 'moq')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('price')" class="inline-flex items-center gap-1">
                            Price
                            @if ($sortField === 'price')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4">
                        <button type="button" wire:click="sortBy('is_active')" class="inline-flex items-center gap-1">
                            Status
                            @if ($sortField === 'is_active')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                        </button>
                    </th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    <tr wire:key="product-row-{{ $product->id }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $product->id }}" wire:model.live="selected" class="h-4 w-4 rounded border-slate-300 text-pink-600">
                        </td>
                        <td class="px-5 py-4 font-black">{{ $product->name }}</td>
                        <td class="px-5 py-4">{{ $product->category?->name ?? 'Unassigned' }}</td>
                        <td class="px-5 py-4">{{ $product->moq }}</td>
                        <td class="px-5 py-4">NGN {{ number_format((float) $product->price, 2) }}</td>
                        <td class="px-5 py-4">{{ $product->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.products.edit', $product) }}" class="font-black text-pink-700 hover:text-pink-800">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="ml-4 font-black text-slate-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-500">No products matched your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $products->links() }}</div>
</section>
