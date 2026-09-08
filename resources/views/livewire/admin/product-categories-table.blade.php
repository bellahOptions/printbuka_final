<div>
    <div class="overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-4">Name</th>
                    <th class="px-5 py-4">Parent</th>
                    <th class="px-5 py-4">Tag</th>
                    <th class="px-5 py-4">Products</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr wire:key="product-category-row-{{ $category->id }}">
                        <td class="px-5 py-4 font-black">{{ $category->name }}</td>
                        <td class="px-5 py-4">{{ $category->parent?->name ?? 'Top-level' }}</td>
                        <td class="px-5 py-4">{{ $category->tag }}</td>
                        <td class="px-5 py-4">{{ $category->products_count }}</td>
                        <td class="px-5 py-4">{{ $category->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.product-categories.edit', $category) }}" class="font-black text-pink-700">Edit</a>
                            <form action="{{ route('admin.product-categories.destroy', $category) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="ml-4 font-black text-slate-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">No categories yet.</td>
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
            <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                Load More
            </button>
        </div>
    @endif
</div>
