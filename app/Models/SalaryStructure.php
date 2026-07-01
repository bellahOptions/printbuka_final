<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryStructure extends Model
{
    protected $fillable = [
        'staff_id', 'effective_date', 'basic_salary',
        'housing_allowance', 'transport_allowance', 'medical_allowance', 'other_allowances',
        'pension_deduction', 'tax_deduction', 'other_deductions',
        'is_active', 'notes', 'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'effective_date'      => 'date',
            'basic_salary'        => 'decimal:2',
            'housing_allowance'   => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'medical_allowance'   => 'decimal:2',
            'other_allowances'    => 'decimal:2',
            'pension_deduction'   => 'decimal:2',
            'tax_deduction'       => 'decimal:2',
            'other_deductions'    => 'decimal:2',
            'is_active'           => 'boolean',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function grossSalary(): float
    {
        return (float) $this->basic_salary
            + (float) $this->housing_allowance
            + (float) $this->transport_allowance
            + (float) $this->medical_allowance
            + (float) $this->other_allowances;
    }

    public function totalDeductions(): float
    {
        return (float) $this->pension_deduction
            + (float) $this->tax_deduction
            + (float) $this->other_deductions;
    }

    public function netSalary(): float
    {
        return max(0, $this->grossSalary() - $this->totalDeductions());
    }
}
