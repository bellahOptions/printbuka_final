<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    protected $fillable = [
        'payroll_month', 'payroll_year', 'status',
        'payment_date', 'notes', 'created_by_id',
        'finalized_by_id', 'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_date'  => 'date',
            'finalized_at'  => 'datetime',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by_id');
    }

    public function periodLabel(): string
    {
        return \Carbon\Carbon::createFromDate($this->payroll_year, $this->payroll_month, 1)->format('F Y');
    }

    public function totalNetPayroll(): float
    {
        return (float) $this->entries()->sum('net_salary');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'draft'     => 'bg-amber-100 text-amber-800',
            'finalized' => 'bg-blue-100 text-blue-800',
            'paid'      => 'bg-emerald-100 text-emerald-800',
            default     => 'bg-slate-100 text-slate-700',
        };
    }
}
