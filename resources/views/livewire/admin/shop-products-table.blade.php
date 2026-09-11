<div>
    {{-- Filters / Search --}}
    <div class="pb-card p-4 mb-5">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Search</label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Name, SKU, description…"
                           class="pb-input w-full pl-9" />
                </div>
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Status</label>
                <select wire:model.live="status" class="pb-select">
                    <option value="">All Products</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="featured">Featured</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-slate-500 block mb-1">Stock</label>
                <select wire:model.live="stock" class="pb-select">
                    <option value="">All Stock</option>
                    <option value="out">Out of Stock</option>
                </select>
            </div>
            @if ($search !== '' || $status !== '' || $stock !== '')
                <button type="button" wire:click="clearFilters" class="pb-btn pb-btn-sm pb-btn-ghost">
                    <x-heroicon-o-x-mark class="w-4 h-4" /> Clear
                </button>
            @endif
        </div>
    </div>

    {{-- Product List --}}
    @if ($products->isEmpty())
        <div class="pb-card py-20 text-center">
            <x-heroicon-o-shopping-bag class="w-14 h-14 text-slate-200 mx-auto mb-4" />
            <p class="font-black text-slate-700 text-lg">No products found</p>
            <p class="text-sm text-slate-400 mt-1 mb-6">
                {{ ($search !== '' || $status !== '' || $stock !== '') ? 'Try adjusting your filters.' : 'Start by adding your first shop product.' }}
            </p>
            @if ($search === '' && $status === '' && $stock === '')
                <a href="{{ route('admin.shop-products.create') }}"
                   class="pb-btn pb-btn-md pb-btn-primary">
                    <x-heroicon-o-plus class="w-4 h-4" /> Add First Product
                </a>
            @endif
        </div>
    @else
        <div class="space-y-3">
            @foreach ($products as $product)
            <div wire:key="shop-product-{{ $product->id }}" class="pb-card hover:shadow-md transition-shadow duration-200 overflow-hidden">
                <div class="flex items-start gap-4 p-4">

                    {{-- Product Image --}}
                    <div class="shrink-0">
                        @if ($product->featuredImageUrl())
                            <img src="{{ $product->featuredImageUrl() }}" alt="{{ $product->name }}"
                                 class="w-20 h-20 rounded-xl object-cover border border-slate-100"
                                 onerror="this.onerror=null;this.src='{{ asset('img/product-placeholder.svg') }}';" />
                        @else
                            <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center border border-slate-100">
                                <x-heroicon-o-photo class="w-7 h-7 text-slate-300" />
                            </div>
                        @endif
                    </div>

                    {{-- Main content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-start justify-between gap-2 mb-1.5">
                            <div class="min-w-0">
                                <h3 class="font-black text-slate-900 text-base leading-tight truncate">{{ $product->name }}</h3>
                                @if ($product->sku)
                                    <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $product->sku }}</p>
                                @endif
                            </div>
                            {{-- Status badges --}}
                            <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                                @if ($product->is_active)
                                    <span class="pb-badge pb-badge-success text-xs">Active</span>
                                @else
                                    <span class="pb-badge pb-badge-secondary text-xs">Inactive</span>
                                @endif
                                @if ($product->is_featured)
                                    <span class="pb-badge pb-badge-warning text-xs">
                                        <x-heroicon-s-star class="w-3 h-3" /> Featured
                                    </span>
                                @endif
                                @if ($product->isOnSale())
                                    <span class="pb-badge pb-badge-info text-xs">Sale</span>
                                @endif
                            </div>
                        </div>

                        @if ($product->short_description)
                            <p class="text-xs text-slate-500 leading-relaxed mb-2 line-clamp-2">{{ $product->short_description }}</p>
                        @endif

                        {{-- Meta row --}}
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 mt-2">
                            {{-- Pricing --}}
                            <div class="flex items-baseline gap-2">
                                @if ($product->isOnSale())
                                    <span class="text-lg font-black text-pink-600">₦{{ number_format((float) $product->sale_price, 0) }}</span>
                                    <span class="text-sm text-slate-400 line-through">₦{{ number_format((float) $product->price, 0) }}</span>
                                @else
                                    <span class="text-lg font-black text-slate-900">₦{{ number_format((float) $product->price, 0) }}</span>
                                @endif
                            </div>

                            {{-- Stock --}}
                            <div class="flex items-center gap-1.5 text-xs">
                                @if ($product->manage_stock)
                                    @if ($product->isInStock())
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span class="font-bold text-emerald-700">{{ $product->stock_quantity }} in stock</span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        <span class="font-bold text-red-600">Out of stock</span>
                                    @endif
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    <span class="text-slate-400 font-bold">Unlimited stock</span>
                                @endif
                            </div>

                            {{-- Options --}}
                            @if ($product->option_groups_count > 0)
                                <div class="flex items-center gap-1.5 text-xs">
                                    <x-heroicon-o-adjustments-horizontal class="w-3.5 h-3.5 text-violet-500" />
                                    <span class="text-slate-500 font-bold">
                                        {{ $product->option_groups_count }} option group{{ $product->option_groups_count !== 1 ? 's' : '' }}
                                    </span>
                                </div>
                            @endif

                            {{-- Views --}}
                            <div class="flex items-center gap-1.5 text-xs">
                                <x-heroicon-o-eye class="w-3.5 h-3.5 text-slate-400" />
                                <span class="text-slate-400 font-bold">{{ number_format((int) $product->view_count) }} views</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions column --}}
                    <div class="flex flex-col items-end gap-2 shrink-0 self-center">
                        <a href="{{ route('admin.shop-products.edit', $product) }}"
                           class="pb-btn pb-btn-sm pb-btn-ink w-full">
                            <x-heroicon-o-pencil-square class="w-4 h-4" /> Edit
                        </a>
                        <div class="flex items-center gap-1 w-full">
                            <a href="{{ route('shop.show', $product) }}" target="_blank"
                               class="pb-btn pb-btn-sm pb-btn-ghost flex-1"
                               title="View in shop">
                                <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                            </a>
                            <form action="{{ route('admin.shop-products.destroy', $product) }}" method="POST"
                                  class="flex-1"
                                  onsubmit="return confirm('Delete \'{{ addslashes($product->name) }}\'?\nThis cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="pb-btn pb-btn-sm pb-btn-ghost text-red-500 hover:text-red-600 hover:bg-red-50 w-full">
                                    <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- Bottom accent for inactive products --}}
                @if (! $product->is_active)
                    <div class="h-0.5 bg-slate-200"></div>
                @elseif ($product->is_featured)
                    <div class="h-0.5 bg-gradient-to-r from-amber-400 to-orange-300"></div>
                @else
                    <div class="h-0.5 bg-gradient-to-r from-pink-500/20 to-transparent"></div>
                @endif
            </div>
            @endforeach
        </div>

        <p class="mt-5 text-xs font-bold text-slate-400">
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
    @endif
</div>
