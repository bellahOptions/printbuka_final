<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollEntry extends Model
{
    protected $fillable = [
        'payroll_run_id', 'staff_id',
        'basic_salary', 'housing_allowance', 'transport_allowance',
        'medical_allowance', 'other_allowances', 'gross_salary',
        'pension_deduction', 'tax_deduction', 'other_deductions',
        'total_deductions', 'net_salary',
        'payment_status', 'payment_method', 'payment_reference', 'paid_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary'        => 'decimal:2',
            'housing_allowance'   => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'medical_allowance'   => 'decimal:2',
            'other_allowances'    => 'decimal:2',
            'gross_salary'        => 'decimal:2',
            'pension_deduction'   => 'decimal:2',
            'tax_deduction'       => 'decimal:2',
            'other_deductions'    => 'decimal:2',
            'total_deductions'    => 'decimal:2',
            'net_salary'          => 'decimal:2',
            'paid_at'             => 'datetime',
        ];
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
