@extends('layouts.admin')
@section('title', 'Inventory')
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title text-xl font-black mb-2">Inventory</h1>
            <p class="text-sm text-slate-400">Track raw materials, consumables and equipment/assets used in production.</p>
        </div>
        @if(auth()->user()?->canAdmin('inventory.manage'))
            <a href="{{ route('admin.inventory.create') }}"
               class="pb-btn pb-btn-md pb-btn-primary self-start">
                <x-heroicon-o-plus class="w-4 h-4" /> Add Item
            </a>
        @endif
    </div>
</div>

@if(session('status'))
    <div class="pb-alert pb-alert-success mb-5">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <a href="{{ route('admin.inventory.index') }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ !request()->hasAny(['status']) ? 'ring-2 ring-pink-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 group-hover:bg-pink-50 transition">
            <x-heroicon-o-archive-box class="w-5 h-5 text-slate-500 group-hover:text-pink-600 transition" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-slate-400">Total Items</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.inventory.index', ['status' => 'active']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'active' ? 'ring-2 ring-emerald-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-emerald-600">Active</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['active']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.inventory.index', ['item_type' => 'equipment']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('item_type') === 'equipment' ? 'ring-2 ring-blue-500' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
            <x-heroicon-o-wrench-screwdriver class="w-5 h-5 text-blue-600" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-blue-600">Equipment</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($stats['equipment']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'low_stock' ? 'ring-2 ring-amber-500' : '' }}">
        <div class="w-10 h-10 rounded-xl {{ $stats['low_stock'] > 0 ? 'bg-amber-50' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 {{ $stats['low_stock'] > 0 ? 'text-amber-500' : 'text-slate-400' }}" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase {{ $stats['low_stock'] > 0 ? 'text-amber-600' : 'text-slate-400' }}">Low Stock</p>
            <p class="text-2xl font-black {{ $stats['low_stock'] > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ number_format($stats['low_stock']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.inventory.index', ['status' => 'out_of_stock']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'out_of_stock' ? 'ring-2 ring-red-500' : '' }}">
        <div class="w-10 h-10 rounded-xl {{ $stats['out_of_stock'] > 0 ? 'bg-red-50' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
            <x-heroicon-o-archive-box-x-mark class="w-5 h-5 {{ $stats['out_of_stock'] > 0 ? 'text-red-500' : 'text-slate-400' }}" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase {{ $stats['out_of_stock'] > 0 ? 'text-red-600' : 'text-slate-400' }}">Out of Stock</p>
            <p class="text-2xl font-black {{ $stats['out_of_stock'] > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ number_format($stats['out_of_stock']) }}</p>
        </div>
    </a>
    <a href="{{ route('admin.inventory.index', ['status' => 'due_for_service']) }}"
       class="pb-card p-4 flex items-center gap-4 hover:shadow transition group {{ request('status') === 'due_for_service' ? 'ring-2 ring-violet-500' : '' }}">
        <div class="w-10 h-10 rounded-xl {{ $stats['due_for_service'] > 0 ? 'bg-violet-50' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
            <x-heroicon-o-clock class="w-5 h-5 {{ $stats['due_for_service'] > 0 ? 'text-violet-600' : 'text-slate-400' }}" />
        </div>
        <div>
            <p class="text-xs font-bold uppercase {{ $stats['due_for_service'] > 0 ? 'text-violet-600' : 'text-slate-400' }}">Due for Service</p>
            <p class="text-2xl font-black {{ $stats['due_for_service'] > 0 ? 'text-violet-600' : 'text-slate-900' }}">{{ number_format($stats['due_for_service']) }}</p>
        </div>
    </a>
</div>

<div class="pb-card p-4 mb-6">
    <p class="text-xs font-bold uppercase text-slate-400 mb-1">Total Stock Value</p>
    <p class="text-2xl font-black text-slate-900">₦{{ number_format((float) $stats['total_value'], 2) }}</p>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.inventory.index') }}" class="pb-card p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]">
        <label class="pb-label text-xs">Search</label>
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Name, SKU or supplier..."
               class="pb-input w-full" />
    </div>
    <div class="min-w-[180px]">
        <label class="pb-label text-xs">Category</label>
        <select name="category" class="pb-select w-full">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" @selected($filters['category'] === $cat)>{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-[160px]">
        <label class="pb-label text-xs">Item Type</label>
        <select name="item_type" class="pb-select w-full">
            <option value="">All Types</option>
            <option value="consumable" @selected($filters['itemType'] === 'consumable')>Consumable Material</option>
            <option value="equipment" @selected($filters['itemType'] === 'equipment')>Equipment / Asset</option>
        </select>
    </div>
    <button type="submit" class="pb-btn pb-btn-md pb-btn-secondary">
        <x-heroicon-o-magnifying-glass class="w-4 h-4" /> Filter
    </button>
    @if($filters['search'] !== '' || $filters['category'] !== '' || $filters['status'] !== '' || $filters['itemType'] !== '')
        <a href="{{ route('admin.inventory.index') }}" class="pb-btn pb-btn-md pb-btn-ghost">Clear</a>
    @endif
    @if($filters['status'] !== '')
        <input type="hidden" name="status" value="{{ $filters['status'] }}" />
    @endif
</form>

<div class="pb-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="pb-table w-full">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Unit Cost</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->imageUrl() ?: asset('img/product-placeholder.svg') }}"
                                     alt="{{ $item->name }}"
                                     class="w-10 h-10 rounded-lg object-cover border border-slate-200 bg-slate-50 shrink-0" />
                                <div>
                                    <p class="font-bold text-slate-900">{{ $item->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $item->sku }}{{ $item->serial_number ? ' · S/N '.$item->serial_number : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($item->isEquipment())
                                <span class="pb-badge pb-badge-info">Equipment</span>
                                @if($item->condition)
                                    <p class="text-xs text-slate-400 mt-1">{{ $item->condition }}</p>
                                @endif
                            @else
                                <span class="pb-badge pb-badge-secondary">Consumable</span>
                            @endif
                        </td>
                        <td class="text-sm text-slate-600">{{ $item->category ?: '—' }}</td>
                        <td class="text-sm">
                            <span class="font-black {{ $item->isOutOfStock() ? 'text-red-600' : ($item->isLowStock() ? 'text-amber-600' : 'text-slate-900') }}">
                                {{ number_format($item->quantity_on_hand) }}
                            </span>
                            <span class="text-slate-400">{{ $item->unit }}</span>
                        </td>
                        <td class="text-sm text-slate-600">
                            {{ $item->unit_cost !== null ? '₦'.number_format((float) $item->unit_cost, 2) : '—' }}
                        </td>
                        <td>
                            @if(! $item->is_active)
                                <span class="pb-badge pb-badge-secondary">Inactive</span>
                            @elseif($item->isOutOfStock())
                                <span class="pb-badge pb-badge-danger">Out of stock</span>
                            @elseif($item->isLowStock())
                                <span class="pb-badge pb-badge-warning">Low stock</span>
                            @else
                                <span class="pb-badge pb-badge-success">In stock</span>
                            @endif
                            @if($item->isDueForService())
                                <span class="pb-badge pb-badge-purple">Due for service</span>
                            @endif
                        </td>
                        <td class="text-xs text-slate-400">{{ $item->updated_at->diffForHumans() }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.inventory.show', $item) }}" class="pb-btn pb-btn-sm pb-btn-ghost">View</a>
                            @if(auth()->user()?->canAdmin('inventory.manage'))
                                <a href="{{ route('admin.inventory.edit', $item) }}" class="pb-btn pb-btn-sm pb-btn-ghost">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="pb-empty">No inventory items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $items->links() }}
</div>

@endsection
