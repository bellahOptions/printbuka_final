<?php

namespace App\Models;

use App\Support\ExecutiveAlert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceEntry extends Model
{
    protected static function booted(): void
    {
        static::created(function (FinanceEntry $entry): void {
            $label = $entry->type === 'income' ? 'Income' : 'Expense';

            ExecutiveAlert::send(
                title: 'New Finance Entry: '.$label,
                body: trim($entry->category.' — ₦'.number_format((float) $entry->amount, 2).($entry->description ? ' ('.$entry->description.')' : '')),
                type: 'finance_entry_created',
                data: ['entry_id' => $entry->id, 'action_url' => route('admin.finance.show', $entry)],
                excludeUserId: $entry->user_id,
            );
        });
    }

    protected $fillable = [
        'idempotency_key',
        'order_id',
        'user_id',
        'last_edited_by',
        'last_edited_at',
        'entry_date',
        'type',
        'entry_type',
        'status',
        'refunded_by_id',
        'refunded_at',
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
            'last_edited_at' => 'datetime',
            'refunded_at' => 'datetime',
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

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'refunded' => 'Refunded',
            default => 'Completed',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'refunded' => 'bg-red-100 text-red-700',
            default => 'bg-emerald-100 text-emerald-700',
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

    public function lastEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by_id');
    }
}
