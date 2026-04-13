<?php

use Livewire\Component;
use App\Models\Product;

new class extends Component
{
    public $query = '';
    public $variant = 'default';

    public function render()
    {
        $query = trim($this->query);

        return $this->view([
            'products' => $query === ''
                ? collect()
                : Product::query()
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%")
                    ->limit(8)
                    ->get(),
        ]);
    }
}
?>

<div @class([
    'w-full',
    'max-w-3xl' => $variant !== 'nav',
    'max-w-none' => $variant === 'nav',
])>
    <div @class([
        'flex w-full rounded-md bg-white',
        'flex-col gap-3 p-2 shadow-xl shadow-cyan-950/10 sm:flex-row' => $variant !== 'nav',
        'items-center border border-slate-200 p-1 shadow-sm' => $variant === 'nav',
    ])>
        <input
            type="text"
            wire:model.live.debounce.300ms="query"
            @class([
                'w-full rounded-md font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:ring-4 focus:ring-pink-100',
                'min-h-14 border border-slate-200 px-4 text-base' => $variant !== 'nav',
                'min-h-10 border-0 px-3 text-sm focus:ring-0' => $variant === 'nav',
            ])
            placeholder="{{ $variant === 'nav' ? 'Search products...' : 'Search print products, branded gifts, mugs, shirts or packaging' }}"
        />
        <button
            type="button"
            @class([
                'rounded-md bg-pink-600 font-black text-white shadow-sm shadow-pink-200 transition hover:bg-pink-700',
                'min-h-14 px-6 text-sm sm:w-52' => $variant !== 'nav',
                'min-h-10 px-4 text-xs' => $variant === 'nav',
            ])
        >
            Search
        </button>
    </div>

    <div class="mt-4">
        <div wire:loading wire:target="query" class="text-sm text-pink-600">
            Searching products...
        </div>

        @if (trim($query) !== '')
            <div @class([
                'mt-3 space-y-2',
                'max-h-80 overflow-auto rounded-md border border-slate-100 bg-white p-2 shadow-xl shadow-slate-900/10' => $variant === 'nav',
            ])>
                @forelse ($products as $product)
                    <article class="rounded-md border border-slate-100 bg-white p-4 shadow-sm">
                        <h2 class="font-semibold text-gray-900">
                            <a href="{{ route('products.show', $product) }}" class="transition hover:text-pink-700">{{ $product->name }}</a>
                        </h2>
                        <p class="text-sm text-gray-600">{{ $product->short_description }}</p>
                        <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-500">
                            <span>MOQ: {{ $product->moq }}</span>
                            <span>Price: NGN {{ number_format($product->price, 2) }}</span>
                            <span>{{ $product->paper_size }}</span>
                            <span>{{ $product->paper_density }}</span>
                        </div>
                        <a href="{{ route('orders.create', $product) }}" class="mt-3 inline-flex rounded-md bg-slate-950 px-4 py-2 text-xs font-black text-white transition hover:bg-pink-700">Start Order</a>
                    </article>
                @empty
                    <p class="text-sm text-gray-600">No products found for "{{ $query }}".</p>
                @endforelse
            </div>
        @endif
    </div>
</div>
