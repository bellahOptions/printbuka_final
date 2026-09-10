<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\User;
use App\Services\CloudinaryUploadService;
use App\Support\CloudinaryUrl;
use App\Support\ExecutiveAlert;
use App\Support\LivewireSecureUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminInventoryController extends Controller
{
    public function index(Request $request): View
    {
        $search   = trim((string) $request->input('search', ''));
        $category = (string) $request->input('category', '');
        $status   = (string) $request->input('status', '');
        $itemType = (string) $request->input('item_type', '');

        $query = InventoryItem::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if (in_array($itemType, InventoryItem::ITEM_TYPES, true)) {
            $query->where('item_type', $itemType);
        }

        match ($status) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            'low_stock' => $query->lowStock(),
            'out_of_stock' => $query->outOfStock(),
            'due_for_service' => $query->dueForService(),
            default => null,
        };

        $items = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'total' => InventoryItem::count(),
            'active' => InventoryItem::active()->count(),
            'equipment' => InventoryItem::equipment()->count(),
            'low_stock' => InventoryItem::lowStock()->count(),
            'out_of_stock' => InventoryItem::outOfStock()->count(),
            'due_for_service' => InventoryItem::dueForService()->count(),
            'total_value' => InventoryItem::query()->sum(DB::raw('quantity_on_hand * COALESCE(unit_cost, 0)')),
        ];

        return view('admin.inventory.index', [
            'items' => $items,
            'stats' => $stats,
            'filters' => compact('search', 'category', 'status', 'itemType'),
            'categories' => InventoryItem::CATEGORIES,
        ]);
    }

    public function create(): View
    {
        return view('admin.inventory.form', [
            'item' => null,
            'categories' => InventoryItem::CATEGORIES,
            'units' => InventoryItem::UNITS,
            'conditions' => InventoryItem::CONDITIONS,
            'staff' => $this->staffOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $item = InventoryItem::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        $imageUpdates = $this->syncImage($request, $item);
        if ($imageUpdates !== []) {
            $item->update($imageUpdates);
        }

        if ($item->quantity_on_hand > 0) {
            InventoryStockMovement::create([
                'inventory_item_id' => $item->id,
                'change' => $item->quantity_on_hand,
                'balance_after' => $item->quantity_on_hand,
                'reason' => 'initial',
                'notes' => 'Initial stock recorded when item was created.',
                'created_by' => $request->user()->id,
            ]);
        }

        ExecutiveAlert::send(
            title: 'New Inventory Item',
            body: $request->user()->displayName().' added "'.$item->name.'" to inventory.',
            type: 'inventory_created',
            data: ['item_id' => $item->id, 'action_url' => route('admin.inventory.show', $item)],
            excludeUserId: $request->user()->id,
        );

        return redirect()->route('admin.inventory.index')->with('status', 'Inventory item created.');
    }

    public function show(InventoryItem $inventoryItem): View
    {
        return view('admin.inventory.show', [
            'item' => $inventoryItem,
            'movements' => $inventoryItem->stockMovements()->with('createdBy')->paginate(20),
        ]);
    }

    public function edit(InventoryItem $inventoryItem): View
    {
        return view('admin.inventory.form', [
            'item' => $inventoryItem,
            'categories' => InventoryItem::CATEGORIES,
            'units' => InventoryItem::UNITS,
            'conditions' => InventoryItem::CONDITIONS,
            'staff' => $this->staffOptions(),
        ]);
    }

    public function update(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $validated = $this->validated($request, $inventoryItem);

        // Stock quantity is never edited directly here — it only changes
        // through recorded movements (see adjustStock) so the balance stays
        // auditable.
        unset($validated['quantity_on_hand']);

        $inventoryItem->update($validated);

        $imageUpdates = $this->syncImage($request, $inventoryItem);
        if ($imageUpdates !== []) {
            $inventoryItem->update($imageUpdates);
        }

        return redirect()->route('admin.inventory.index')->with('status', 'Inventory item updated.');
    }

    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $this->deleteImage((string) ($inventoryItem->image ?? ''));

        $inventoryItem->delete();

        return back()->with('status', 'Inventory item deleted.');
    }

    public function adjustStock(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => ['required', Rule::in(['add', 'remove'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', Rule::in(InventoryItem::MOVEMENT_REASONS)],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $change = $validated['direction'] === 'add' ? $validated['quantity'] : -$validated['quantity'];
        $newBalance = $inventoryItem->quantity_on_hand + $change;

        if ($newBalance < 0) {
            return back()->withErrors(['quantity' => 'Cannot remove more than the current stock on hand ('.$inventoryItem->quantity_on_hand.').'])->withInput();
        }

        DB::transaction(function () use ($inventoryItem, $change, $newBalance, $validated, $request): void {
            InventoryStockMovement::create([
                'inventory_item_id' => $inventoryItem->id,
                'change' => $change,
                'balance_after' => $newBalance,
                'reason' => $validated['reason'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            $inventoryItem->update(['quantity_on_hand' => $newBalance]);
        });

        return back()->with('status', 'Stock movement recorded.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?InventoryItem $item = null): array
    {
        $id = $item?->id ?? 'NULL';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:30', "unique:inventory_items,sku,{$id}"],
            'category' => ['nullable', 'string', Rule::in(InventoryItem::CATEGORIES)],
            'item_type' => ['required', Rule::in(InventoryItem::ITEM_TYPES)],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'condition' => ['nullable', 'string', Rule::in(InventoryItem::CONDITIONS)],
            'unit' => ['required', 'string', Rule::in(InventoryItem::UNITS)],
            'quantity_on_hand' => ['nullable', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expiry' => ['nullable', 'date'],
            'last_serviced_at' => ['nullable', 'date'],
            'next_service_due' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isEquipment = $data['item_type'] === 'equipment';

        return [
            'name' => $data['name'],
            'sku' => filled($data['sku'] ?? '') ? $data['sku'] : null,
            'category' => $data['category'] ?? null,
            'item_type' => $data['item_type'],
            'serial_number' => $isEquipment ? ($data['serial_number'] ?? null) : null,
            'condition' => $isEquipment ? ($data['condition'] ?? null) : null,
            'unit' => $data['unit'],
            'quantity_on_hand' => $item ? $item->quantity_on_hand : ($data['quantity_on_hand'] ?? 0),
            'reorder_level' => $data['reorder_level'] ?? null,
            'unit_cost' => filled($data['unit_cost'] ?? '') ? $data['unit_cost'] : null,
            'supplier' => $data['supplier'] ?? null,
            'location' => $data['location'] ?? null,
            'assigned_to' => $isEquipment ? ($data['assigned_to'] ?? null) : null,
            'purchase_date' => $isEquipment ? ($data['purchase_date'] ?? null) : null,
            'warranty_expiry' => $isEquipment ? ($data['warranty_expiry'] ?? null) : null,
            'last_serviced_at' => $isEquipment ? ($data['last_serviced_at'] ?? null) : null,
            'next_service_due' => $isEquipment ? ($data['next_service_due'] ?? null) : null,
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    private function staffOptions()
    {
        return User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'role']);
    }

    /** @return array<string, mixed> */
    private function syncImage(Request $request, InventoryItem $item): array
    {
        $updates = [];
        $cloudinary = app(CloudinaryUploadService::class);

        if ($request->boolean('remove_image') && filled($item->image)) {
            $this->deleteImage((string) $item->image);
            $updates['image'] = null;
        }

        if ($request->hasFile('image')) {
            if (filled($item->image)) {
                $this->deleteImage((string) $item->image);
            }
            $result = $cloudinary->storeToBoth($request->file('image'), 'inventory/items', 'inventory/items');
            $updates['image'] = $result['cloudinary_public_id'] ?? $result['path'];
        } elseif (filled($request->input('image_path'))) {
            $livewirePath = LivewireSecureUploads::consumePath(
                $request,
                (string) $request->input('image_path'),
                ['inventory/items']
            );

            if (! $livewirePath) {
                throw ValidationException::withMessages([
                    'image' => 'The uploaded image is invalid or expired. Please upload again.',
                ]);
            }

            if (filled($item->image)) {
                $this->deleteImage((string) $item->image);
            }

            if (CloudinaryUrl::isCloudinaryResource($livewirePath)) {
                $updates['image'] = $livewirePath;
            } elseif (CloudinaryUrl::isConfigured()) {
                $fullPath = Storage::disk('public')->path($livewirePath);
                $result = $cloudinary->upload($fullPath, ['folder' => 'inventory/items']);
                $updates['image'] = $result['public_id'] ?? $livewirePath;
            } else {
                $updates['image'] = $livewirePath;
            }
        }

        return $updates;
    }

    private function deleteImage(string $path): void
    {
        if ($path === '') {
            return;
        }

        if (CloudinaryUrl::isCloudinaryResource($path)) {
            try {
                app(CloudinaryUploadService::class)->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        Storage::disk('public')->delete($path);
    }
}
