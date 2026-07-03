<?php

use Livewire\Component;
use App\Support\PriceList;

new class extends Component
{
    public string $query = '';

    public function render()
    {
        return $this->view([
            'results' => PriceList::search($this->query, 20),
        ]);
    }
}
?>

<div class="w-full">
    <div class="relative">
        <input
            type="text"
            wire:model.live.debounce.300ms="query"
            class="w-full rounded-md border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20"
            placeholder="Search a product or service to quote a client..."
        />
    </div>

    <div wire:loading wire:target="query" class="mt-2 text-sm font-semibold text-pink-600">Searching...</div>

    @if (trim($query) !== '')
        <div class="mt-4 space-y-2">
            @forelse ($results as $result)
                <article class="flex items-center justify-between gap-4 rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="truncate font-black text-slate-900">{{ $result['label'] }}</span>
                            <span @class([
                                'shrink-0 rounded-full px-2 py-0.5 text-[10px] font-black uppercase tracking-wide',
                                'bg-pink-100 text-pink-700' => $result['type'] === 'product',
                                'bg-cyan-100 text-cyan-700' => $result['type'] === 'service',
                            ])>{{ $result['type'] }}</span>
                        </div>
                        <p class="mt-1 text-lg font-black text-emerald-700">₦{{ number_format($result['price'], 2) }}</p>
                    </div>
                    <a href="{{ $result['edit_url'] }}" class="shrink-0 rounded-md bg-slate-900 px-4 py-2 text-xs font-black text-white transition hover:bg-pink-600">
                        Edit pricing
                    </a>
                </article>
            @empty
                <p class="py-6 text-center text-sm text-slate-500">No products or services matched "<strong>{{ $query }}</strong>".</p>
            @endforelse
        </div>
    @endif
</div>
