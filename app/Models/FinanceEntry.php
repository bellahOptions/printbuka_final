<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceEntry extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'entry_date',
        'type',
        'entry_type',
        'category',
        'description',
        'payee',
        'amount',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the display label for the entry_type field.
     */
    public function entryTypeLabel(): string
    {
        return match ($this->entry_type) {
            'credit_from_ceo' => 'Credit from CEO',
            'auto_income' => 'Auto Income (Invoice)',
            default => 'Manual Entry',
        };
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
