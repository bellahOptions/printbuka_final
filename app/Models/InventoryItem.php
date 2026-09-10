<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    /**
     * @var array<int, string>
     */
    public const CATEGORIES = [
        'Paper & Substrate',
        'Ink & Toner',
        'Vinyl & DTF Film',
        'Laser & Engraving Consumables',
        'Packaging & Finishing',
        'Printing Plates & Screens',
        'Machinery & Equipment',
        'Tools & Fixtures',
        'IT & Electronics',
        'Other',
    ];

    /**
     * @var array<int, string>
     */
    public const UNITS = [
        'pieces', 'sheets', 'rolls', 'reams', 'meters', 'liters', 'kg', 'boxes', 'packs',
    ];

    /**
     * consumable = depletable material tracked purely by quantity (paper, ink,
     * vinyl...). equipment = a durable asset also tracked by condition,
     * assignment and service/warranty dates (plates, machines, tools...).
     *
     * @var array<int, string>
     */
    public const ITEM_TYPES = ['consumable', 'equipment'];

    /**
     * @var array<int, string>
     */
    public const CONDITIONS = ['New', 'Good', 'Fair', 'Needs Repair', 'Under Repair', 'Retired'];

    /**
     * @var array<int, string>
     */
    public const MOVEMENT_REASONS = [
        'initial', 'restock', 'usage', 'adjustment', 'damaged', 'return', 'retired', 'other',
    ];

    protected $fillable = [
        'sku', 'sku_sequence', 'name', 'category', 'item_type', 'serial_number',
        'condition', 'unit', 'quantity_on_hand', 'reorder_level', 'unit_cost',
        'supplier', 'location', 'assigned_to', 'purchase_date', 'warranty_expiry',
        'last_serviced_at', 'next_service_due', 'image', 'description',
        'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'integer',
            'reorder_level' => 'integer',
            'unit_cost' => 'decimal:2',
            'is_active' => 'boolean',
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
            'last_serviced_at' => 'date',
            'next_service_due' => 'date',
        ];
    }

    protected static function booted(): void
    {
        // Auto-generate SKU after the record is persisted so we have the ID.
        // Format: INV-YYYY-NNNNN e.g. INV-2026-00003
        static::created(function (InventoryItem $item): void {
            if (filled($item->sku)) {
                return;
            }

            $year     = now()->format('Y');
            $sequence = static::max('sku_sequence') ?? 0;
            $sequence++;

            $sku = 'INV-'.$year.'-'.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);

            $item->updateQuietly(['sku' => $sku, 'sku_sequence' => $sequence]);
        });
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(InventoryStockMovement::class)->latest();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereNotNull('reorder_level')
            ->whereColumn('quantity_on_hand', '<=', 'reorder_level');
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity_on_hand', '<=', 0);
    }

    public function scopeEquipment(Builder $query): Builder
    {
        return $query->where('item_type', 'equipment');
    }

    public function scopeConsumable(Builder $query): Builder
    {
        return $query->where('item_type', 'consumable');
    }

    public function scopeDueForService(Builder $query): Builder
    {
        return $query->where('item_type', 'equipment')
            ->whereNotNull('next_service_due')
            ->whereDate('next_service_due', '<=', now()->addDays(7));
    }

    public function isLowStock(): bool
    {
        return $this->reorder_level !== null && $this->quantity_on_hand <= $this->reorder_level;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity_on_hand <= 0;
    }

    public function isEquipment(): bool
    {
        return $this->item_type === 'equipment';
    }

    public function isDueForService(): bool
    {
        return $this->isEquipment()
            && $this->next_service_due !== null
            && $this->next_service_due->lte(now()->addDays(7));
    }

    public function isUnderWarranty(): bool
    {
        return $this->warranty_expiry !== null && $this->warranty_expiry->isFuture();
    }

    public function totalValue(): float
    {
        return (float) $this->quantity_on_hand * (float) ($this->unit_cost ?? 0);
    }

    public function imageUrl(): ?string
    {
        return MediaUrl::resolve($this->image);
    }
}
