<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'created_by_admin_id',
        'service_type',
        'channel',
        'job_type',
        'size_format',
        'quantity',
        'quote_budget',
        'unit_price',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_city',
        'delivery_address',
        'artwork_notes',
        'job_image_assets',
        'pricing_breakdown',
        'status',
        'job_order_number',
        'priority',
        'is_express',
        'is_sample',
        'brief_received_by_id',
        'brief_received_at',
        'assigned_designer_id',
        'design_started_at',
        'design_approved_by_client',
        'final_design_path',
        'design_approved_at',
        'production_officer_id',
        'production_started_at',
        'material_substrate',
        'paper_density',
        'finish_lamination',
        'qc_checked_by_id',
        'qc_checked_at',
        'qc_result',
        'estimated_delivery_at',
        'actual_delivery_at',
        'delivery_method',
        'dispatched_by_id',
        'client_review_status',
        'after_sales_action',
        'after_sales_resolved_at',
        'amount_paid',
        'payment_status',
        'internal_notes',
        'verified_by_id',
        'verified_at',
        'phase_approval_status',
        'phase_approval_comment',
        'phase_approved_by_id',
        'phase_approved_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'quote_budget' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'job_image_assets' => 'array',
            'pricing_breakdown' => 'array',
            'design_approved_by_client' => 'boolean',
            'is_express' => 'boolean',
            'is_sample' => 'boolean',
            'brief_received_at' => 'datetime',
            'design_started_at' => 'datetime',
            'design_approved_at' => 'datetime',
            'production_started_at' => 'datetime',
            'qc_checked_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
            'actual_delivery_at' => 'datetime',
            'after_sales_resolved_at' => 'datetime',
            'verified_at' => 'datetime',
            'phase_approved_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function briefReceiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brief_received_by_id');
    }

    public function creatorAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_designer_id');
    }

    public function productionOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'production_officer_id');
    }

    public function qcOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qc_checked_by_id');
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function phaseApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'phase_approved_by_id');
    }

    public function displayNumber(): string
    {
        return '#'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }
}
