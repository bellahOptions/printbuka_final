@extends('layouts.admin')
@section('title', $item ? 'Edit Inventory Item' : 'Add Inventory Item')
@section('content')

<div class="pb-page-header">
    <h1 class="pb-page-title text-xl font-black mb-2">{{ $item ? 'Edit Inventory Item' : 'Add Inventory Item' }}</h1>
    <p class="text-sm text-slate-400">{{ $item ? 'Update details for '.$item->name.'.' : 'Register a new material or consumable.' }}</p>
</div>

<form method="POST"
      action="{{ $item ? route('admin.inventory.update', $item) : route('admin.inventory.store') }}"
      enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @csrf
    @if($item)
        @method('PUT')
    @endif

    <div class="lg:col-span-2 space-y-6">
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
            <div class="p-5 space-y-5">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider">Item Details</h2>

                <div>
                    <label class="pb-label">Item Type</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 p-2.5 cursor-pointer has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50">
                            <input type="radio" name="item_type" value="consumable" id="item-type-consumable"
                                   @checked(old('item_type', $item?->item_type ?? 'consumable') === 'consumable')
                                   class="radio radio-xs" onchange="pbToggleInventoryType()" />
                            <span class="text-sm font-bold text-slate-700">Consumable Material</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 p-2.5 cursor-pointer has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50">
                            <input type="radio" name="item_type" value="equipment" id="item-type-equipment"
                                   @checked(old('item_type', $item?->item_type ?? 'consumable') === 'equipment')
                                   class="radio radio-xs" onchange="pbToggleInventoryType()" />
                            <span class="text-sm font-bold text-slate-700">Equipment / Asset</span>
                        </label>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Consumables are depleted by use (paper, ink, vinyl). Equipment is a durable asset tracked by condition and assignment (plates, machines, tools).</p>
                </div>

                <div>
                    <label class="pb-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $item?->name) }}" required
                           class="pb-input w-full @error('name') pb-input-error @enderror" />
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="pb-label">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $item?->sku) }}"
                               placeholder="{{ $item ? '' : 'Auto-generated if left blank' }}"
                               class="pb-input w-full @error('sku') pb-input-error @enderror" />
                        @error('sku') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pb-label">Category</label>
                        <select name="category" class="pb-select w-full">
                            <option value="">— Select —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $item?->category) === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="pb-label">Unit of Measure</label>
                        <select name="unit" required class="pb-select w-full">
                            @foreach($units as $unit)
                                <option value="{{ $unit }}" @selected(old('unit', $item?->unit ?? 'pieces') === $unit)>{{ ucfirst($unit) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pb-label">{{ $item ? 'Current Stock' : 'Initial Stock' }}</label>
                        <input type="number" min="0" name="quantity_on_hand"
                               value="{{ old('quantity_on_hand', $item?->quantity_on_hand ?? 0) }}"
                               @disabled($item)
                               class="pb-input w-full" />
                        @if($item)
                            <p class="text-xs text-slate-400 mt-1">Use "Adjust Stock" on the item page to change this.</p>
                        @endif
                    </div>
                    <div>
                        <label class="pb-label">Reorder Level</label>
                        <input type="number" min="0" name="reorder_level"
                               value="{{ old('reorder_level', $item?->reorder_level) }}"
                               placeholder="Optional"
                               class="pb-input w-full" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="pb-label">Unit Cost (₦)</label>
                        <input type="number" min="0" step="0.01" name="unit_cost"
                               value="{{ old('unit_cost', $item?->unit_cost) }}"
                               placeholder="Optional"
                               class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Supplier</label>
                        <input type="text" name="supplier" value="{{ old('supplier', $item?->supplier) }}"
                               class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Storage Location</label>
                        <input type="text" name="location" value="{{ old('location', $item?->location) }}"
                               placeholder="e.g. Shelf B2"
                               class="pb-input w-full" />
                    </div>
                </div>

                <div>
                    <label class="pb-label">Description / Notes</label>
                    <textarea name="description" rows="3" class="pb-textarea w-full">{{ old('description', $item?->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Equipment-only details --}}
        <div id="equipment-fields" class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
            <div class="p-5 space-y-5">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2">
                    <x-heroicon-o-wrench-screwdriver class="w-4 h-4 text-pink-500" /> Equipment Details
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="pb-label">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $item?->serial_number) }}"
                               class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Condition</label>
                        <select name="condition" class="pb-select w-full">
                            <option value="">— Select —</option>
                            @foreach($conditions as $condition)
                                <option value="{{ $condition }}" @selected(old('condition', $item?->condition) === $condition)>{{ $condition }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="pb-label">Assigned To</label>
                    <select name="assigned_to" class="pb-select w-full">
                        <option value="">— Unassigned —</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" @selected((string) old('assigned_to', $item?->assigned_to) === (string) $member->id)>
                                {{ trim($member->first_name.' '.$member->last_name) ?: $member->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="pb-label">Purchase Date</label>
                        <input type="date" name="purchase_date"
                               value="{{ old('purchase_date', $item?->purchase_date?->format('Y-m-d')) }}"
                               class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Warranty Expiry</label>
                        <input type="date" name="warranty_expiry"
                               value="{{ old('warranty_expiry', $item?->warranty_expiry?->format('Y-m-d')) }}"
                               class="pb-input w-full" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="pb-label">Last Serviced</label>
                        <input type="date" name="last_serviced_at"
                               value="{{ old('last_serviced_at', $item?->last_serviced_at?->format('Y-m-d')) }}"
                               class="pb-input w-full" />
                    </div>
                    <div>
                        <label class="pb-label">Next Service Due</label>
                        <input type="date" name="next_service_due"
                               value="{{ old('next_service_due', $item?->next_service_due?->format('Y-m-d')) }}"
                               class="pb-input w-full" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Image --}}
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
            <div class="p-5 space-y-4">
                <h2 class="font-black text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2">
                    <x-heroicon-o-photo class="w-4 h-4 text-pink-500" /> Item Image
                </h2>

                <livewire:uploads.secure-image-upload
                    :key="'inventory-image-'.($item?->id ?: 'create')"
                    input-name="image_path"
                    directory="inventory/items"
                    :max-size-kb="4096"
                    :max-files="1"
                    :multiple="false"
                    :initial-path="old('image_path')"
                />
                <p class="text-xs text-slate-400">JPG, PNG or WebP · max 4 MB</p>
                @error('image') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                @error('image_path') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror

                @if($item?->image)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs font-bold text-slate-500 mb-2">Current Image</p>
                        <img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}"
                             class="h-32 w-32 rounded-lg border border-slate-200 object-cover bg-white" />
                        <label class="flex items-center gap-2 mt-3 text-xs text-slate-500">
                            <input type="checkbox" name="remove_image" value="1" class="checkbox checkbox-xs" />
                            Remove current image
                        </label>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-pink-500 to-orange-400"></div>
            <div class="p-5 space-y-4">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $item?->is_active ?? true))
                           class="checkbox checkbox-sm" />
                    <div>
                        <p class="font-bold text-slate-900 text-sm">Active</p>
                        <p class="text-xs text-slate-400">Inactive items are hidden from the default list</p>
                    </div>
                </label>

                <button type="submit" class="pb-btn pb-btn-md pb-btn-primary w-full">
                    <x-heroicon-o-check class="w-4 h-4" />
                    {{ $item ? 'Save Changes' : 'Create Item' }}
                </button>
                <a href="{{ route('admin.inventory.index') }}" class="pb-btn pb-btn-md pb-btn-ghost w-full">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<script>
    function pbToggleInventoryType() {
        const isEquipment = document.getElementById('item-type-equipment')?.checked;
        const panel = document.getElementById('equipment-fields');
        if (panel) panel.hidden = !isEquipment;
    }
    document.addEventListener('DOMContentLoaded', pbToggleInventoryType);
</script>

@endsection
