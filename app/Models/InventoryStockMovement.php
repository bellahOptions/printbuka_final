<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStockMovement extends Model
{
    protected $fillable = [
        'inventory_item_id', 'change', 'balance_after', 'reason', 'reference', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'change' => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isAddition(): bool
    {
        return $this->change > 0;
    }

    public function isDeduction(): bool
    {
        return $this->change < 0;
    }

    public function reasonLabel(): string
    {
        return match ($this->reason) {
            'initial' => 'Initial stock',
            'restock' => 'Restock',
            'usage' => 'Used in production',
            'adjustment' => 'Manual adjustment',
            'damaged' => 'Damaged / wastage',
            'return' => 'Returned to stock',
            'retired' => 'Retired / disposed',
            default => 'Other',
        };
    }
}
