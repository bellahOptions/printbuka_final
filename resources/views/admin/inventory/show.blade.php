@extends('layouts.admin')
@section('title', $item->name)
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title text-xl font-black mb-1">{{ $item->name }}</h1>
            <p class="text-sm text-slate-400 flex items-center gap-2 flex-wrap">
                {{ $item->sku }} · {{ $item->category ?: 'Uncategorized' }}
                @if($item->isEquipment())
                    <span class="pb-badge pb-badge-info">Equipment</span>
                @else
                    <span class="pb-badge pb-badge-secondary">Consumable</span>
                @endif
                @if($item->isDueForService())
                    <span class="pb-badge pb-badge-purple">Due for service</span>
                @endif
            </p>
        </div>
        @if(auth()->user()?->canAdmin('inventory.manage'))
            <a href="{{ route('admin.inventory.edit', $item) }}" class="btn btn-neutral font-black gap-2 self-start">
                <x-heroicon-o-pencil class="w-4 h-4" /> Edit Item
            </a>
        @endif
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success mb-5 font-bold">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error mb-5 font-bold">
        <x-heroicon-o-exclamation-circle class="w-5 h-5" /> {{ $errors->first() }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        <div class="pb-card p-5">
            <div class="flex items-start gap-4">
                <img src="{{ $item->imageUrl() ?: asset('img/product-placeholder.svg') }}" alt="{{ $item->name }}"
                     class="w-24 h-24 rounded-xl object-cover border border-slate-200 bg-slate-50 shrink-0" />
                <div class="grid grid-cols-2 gap-4 flex-1">
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Stock on Hand</p>
                        <p class="text-2xl font-black {{ $item->isOutOfStock() ? 'text-red-600' : ($item->isLowStock() ? 'text-amber-600' : 'text-slate-900') }}">
                            {{ number_format($item->quantity_on_hand) }} <span class="text-sm font-bold text-slate-400">{{ $item->unit }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Reorder Level</p>
                        <p class="text-lg font-black text-slate-900">{{ $item->reorder_level !== null ? number_format($item->reorder_level) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Unit Cost</p>
                        <p class="text-lg font-black text-slate-900">{{ $item->unit_cost !== null ? '₦'.number_format((float) $item->unit_cost, 2) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Stock Value</p>
                        <p class="text-lg font-black text-slate-900">₦{{ number_format($item->totalValue(), 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-100 text-sm">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Supplier</p>
                    <p class="font-bold text-slate-700">{{ $item->supplier ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Location</p>
                    <p class="font-bold text-slate-700">{{ $item->location ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Added By</p>
                    <p class="font-bold text-slate-700">{{ $item->createdBy?->displayName() ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Created</p>
                    <p class="font-bold text-slate-700" title="{{ $item->created_at }}">{{ $item->created_at->diffForHumans() }}</p>
                </div>
            </div>

            @if($item->description)
                <p class="text-sm text-slate-600 mt-4 pt-4 border-t border-slate-100">{{ $item->description }}</p>
            @endif
        </div>

        @if($item->isEquipment())
            <div class="pb-card p-5">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2 mb-4">
                    <x-heroicon-o-wrench-screwdriver class="w-4 h-4 text-pink-500" /> Equipment Details
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Serial Number</p>
                        <p class="font-bold text-slate-700">{{ $item->serial_number ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Condition</p>
                        <p class="font-bold text-slate-700">{{ $item->condition ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Assigned To</p>
                        <p class="font-bold text-slate-700">{{ $item->assignedTo?->displayName() ?: 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Purchase Date</p>
                        <p class="font-bold text-slate-700">{{ $item->purchase_date?->format('d M Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Warranty Expiry</p>
                        <p class="font-bold {{ $item->isUnderWarranty() ? 'text-emerald-600' : 'text-slate-700' }}">
                            {{ $item->warranty_expiry?->format('d M Y') ?: '—' }}
                            @if($item->isUnderWarranty())
                                <span class="pb-badge pb-badge-success ml-1">Active</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Last Serviced</p>
                        <p class="font-bold text-slate-700">{{ $item->last_serviced_at?->format('d M Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-400">Next Service Due</p>
                        <p class="font-bold {{ $item->isDueForService() ? 'text-violet-600' : 'text-slate-700' }}">
                            {{ $item->next_service_due?->format('d M Y') ?: '—' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Stock movement history --}}
        <div class="pb-card overflow-hidden">
            <div class="p-5 pb-0">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2">
                    <x-heroicon-o-clock class="w-4 h-4 text-pink-500" /> Stock Movement History
                </h2>
            </div>
            <div class="overflow-x-auto mt-3">
                <table class="pb-table w-full">
                    <thead>
                        <tr>
                            <th>Date &amp; Time</th>
                            <th>Change</th>
                            <th>Balance After</th>
                            <th>Reason</th>
                            <th>Reference</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td class="text-xs text-slate-500" title="{{ $movement->created_at }}">
                                    {{ $movement->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="font-black {{ $movement->isAddition() ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $movement->isAddition() ? '+' : '' }}{{ number_format($movement->change) }}
                                </td>
                                <td class="font-bold text-slate-700">{{ number_format($movement->balance_after) }}</td>
                                <td class="text-sm text-slate-600">
                                    {{ $movement->reasonLabel() }}
                                    @if($movement->notes)
                                        <p class="text-xs text-slate-400">{{ $movement->notes }}</p>
                                    @endif
                                </td>
                                <td class="text-sm text-slate-500">{{ $movement->reference ?: '—' }}</td>
                                <td class="text-sm text-slate-500">{{ $movement->createdBy?->displayName() ?: 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="pb-empty">No stock movements recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>

    <div class="space-y-6">
        @if(auth()->user()?->canAdmin('inventory.manage'))
            <div class="pb-card overflow-hidden">
                <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
                <div class="p-5 space-y-4">
                    <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider">Adjust Stock</h2>
                    <form method="POST" action="{{ route('admin.inventory.adjust-stock', $item) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 p-2.5 cursor-pointer has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="direction" value="add" checked class="radio radio-xs" />
                                <span class="text-sm font-bold text-emerald-700">Add</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 p-2.5 cursor-pointer has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="radio" name="direction" value="remove" class="radio radio-xs" />
                                <span class="text-sm font-bold text-red-700">Remove</span>
                            </label>
                        </div>

                        <div>
                            <label class="pb-label">Quantity</label>
                            <input type="number" name="quantity" min="1" required class="pb-input w-full" />
                        </div>

                        <div>
                            <label class="pb-label">Reason</label>
                            <select name="reason" required class="pb-select w-full">
                                @foreach(\App\Models\InventoryItem::MOVEMENT_REASONS as $reason)
                                    @if($reason !== 'initial')
                                        <option value="{{ $reason }}">{{ ucfirst($reason) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="pb-label">Reference</label>
                            <input type="text" name="reference" placeholder="e.g. job order or invoice #"
                                   class="pb-input w-full" />
                        </div>

                        <div>
                            <label class="pb-label">Notes</label>
                            <textarea name="notes" rows="2" class="pb-textarea w-full"></textarea>
                        </div>

                        <button type="submit" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black w-full">
                            Record Movement
                        </button>
                    </form>
                </div>
            </div>

            <div class="pb-card p-5">
                <form method="POST" action="{{ route('admin.inventory.destroy', $item) }}"
                      onsubmit="return confirm('Delete this inventory item and its stock history? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost text-red-600 font-black w-full">
                        <x-heroicon-o-trash class="w-4 h-4" /> Delete Item
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@endsection
