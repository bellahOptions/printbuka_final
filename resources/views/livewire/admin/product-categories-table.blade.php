<div>
    <div class="pb-table-wrapper">
        <table class="pb-table min-w-[760px]">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Tag</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr wire:key="product-category-row-{{ $category->id }}">
                        <td class="font-black text-slate-900">{{ $category->name }}</td>
                        <td>{{ $category->parent?->name ?? 'Top-level' }}</td>
                        <td>{{ $category->tag }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>{{ $category->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.product-categories.edit', $category) }}" class="pb-btn pb-btn-sm pb-btn-ghost">Edit</a>
                            <form action="{{ route('admin.product-categories.destroy', $category) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="pb-btn pb-btn-sm pb-btn-ghost text-red-500 hover:text-red-600 hover:bg-red-50">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="pb-empty">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs font-bold text-slate-400">
        Showing {{ number_format($categories->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('category', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more categories as you scroll...
            </span>
            <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more categories...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</div>
