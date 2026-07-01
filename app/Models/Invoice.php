<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'imported_customer_id',
        'invoice_number',
        'external_document_id',
        'external_customer_id',
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
        'last_unpaid_reminder_sent_at',
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
            'last_unpaid_reminder_sent_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function importedCustomer(): BelongsTo
    {
        return $this->belongsTo(ImportedCustomer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function amountPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balance(): float
    {
        return max(0, (float) $this->total_amount - $this->amountPaid());
    }

    public function paymentPercentage(): float
    {
        $total = (float) $this->total_amount;

        if ($total <= 0) {
            return 0;
        }

        return min(100, round(($this->amountPaid() / $total) * 100, 2));
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
