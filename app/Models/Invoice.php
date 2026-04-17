<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'invoice_number',
        'payment_reference',
        'payment_gateway',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'issued_at',
        'due_at',
        'sent_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'issued_at' => 'datetime',
            'due_at' => 'datetime',
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isQuotation(): bool
    {
        return strtolower((string) ($this->order?->service_type ?? '')) === 'quote';
    }

    public function documentTypeLabel(): string
    {
        return $this->isQuotation() ? 'Quotation' : 'Invoice';
    }
}
