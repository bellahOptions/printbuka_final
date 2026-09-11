<?php

use Livewire\Component;
use App\Models\Vendor;

new class extends Component
{
    public string $query = '';

    public string $variant = 'default';

    public function render()
    {
        $q = trim($this->query);

        $vendors = collect();

        if ($q !== '') {
            $vendors = Vendor::query()
                ->search($q)
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        return $this->view(['vendors' => $vendors]);
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
            placeholder="Search vendors by name, contact, email, phone, category or tag..."
        />
    </div>

    <div class="mt-3">
        <div wire:loading wire:target="query" class="text-sm text-pink-600 font-semibold">
            Searching...
        </div>

        @if (trim($query) !== '')
            <div @class([
                'mt-2 space-y-2',
                'max-h-96 overflow-auto rounded-md border border-slate-100 bg-white p-2 shadow-xl shadow-slate-900/10' => $variant === 'nav',
            ])>
                @if ($vendors->isEmpty())
                    <p class="py-6 text-center text-sm text-slate-500">No vendors found for "<strong>{{ $query }}</strong>".</p>
                @else
                    @foreach ($vendors as $vendor)
                        <article class="group flex items-start gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-sm hover:border-pink-200 hover:shadow-md transition-all">
                            @if($vendor->logoUrl())
                                <img src="{{ $vendor->logoUrl() }}" alt="{{ $vendor->displayName() }}"
                                     class="h-12 w-12 rounded-lg object-cover shrink-0 border border-slate-100">
                            @else
                                <div class="h-12 w-12 rounded-lg bg-pink-50 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-black text-pink-500">{{ $vendor->initials() }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                    <a href="{{ route('admin.vendors.show', $vendor) }}" class="font-black text-sm text-slate-900 hover:text-pink-600 transition truncate">{{ $vendor->displayName() }}</a>
                                    <span class="shrink-0 text-[9px] font-black uppercase tracking-wider bg-pink-100 text-pink-700 px-1.5 py-0.5 rounded-full">{{ $vendor->vendor_type }}</span>
                                    @if($vendor->status === 'blacklisted')
                                        <span class="shrink-0 text-[9px] font-black uppercase tracking-wider bg-red-100 text-red-700 px-1.5 py-0.5 rounded-full">Blacklisted</span>
                                    @endif
                                </div>
                                @if($vendor->category)
                                    <p class="text-xs text-slate-500 line-clamp-1">{{ $vendor->category }}</p>
                                @endif
                                <div class="mt-1.5 flex items-center gap-3 text-xs text-slate-400">
                                    @if($vendor->phone)
                                        <span>{{ $vendor->phone }}</span>
                                    @endif
                                    @if($vendor->email)
                                        <span class="truncate">{{ $vendor->email }}</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.vendors.show', $vendor) }}"
                               class="shrink-0 self-center rounded-lg bg-slate-900 group-hover:bg-pink-600 px-3 py-1.5 text-xs font-black text-white transition-colors">
                                View
                            </a>
                        </article>
                    @endforeach
                @endif
            </div>
        @endif
    </div>
</div>
