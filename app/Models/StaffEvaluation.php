<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffEvaluation extends Model
{
    protected $fillable = [
        'staff_id', 'evaluated_by_id', 'period_month', 'period_year',
        'overall_rating', 'punctuality_rating', 'quality_of_work_rating',
        'teamwork_rating', 'communication_rating', 'initiative_rating',
        'strengths', 'areas_for_improvement', 'comments',
        'status', 'staff_acknowledged', 'staff_acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'staff_acknowledged'     => 'boolean',
            'staff_acknowledged_at'  => 'datetime',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function evaluatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by_id');
    }

    public function periodLabel(): string
    {
        return \Carbon\Carbon::createFromDate($this->period_year, $this->period_month, 1)->format('F Y');
    }

    public function averageRating(): float
    {
        $fields = ['punctuality_rating', 'quality_of_work_rating', 'teamwork_rating', 'communication_rating', 'initiative_rating'];
        $values = collect($fields)->map(fn ($f) => $this->{$f})->filter()->values();

        return $values->isNotEmpty() ? round($values->avg(), 1) : (float) $this->overall_rating;
    }

    public function ratingStars(int $rating): string
    {
        return str_repeat('★', $rating).str_repeat('☆', 5 - $rating);
    }
}
