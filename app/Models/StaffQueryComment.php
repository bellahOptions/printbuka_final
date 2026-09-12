<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffQueryComment extends Model
{
    protected $fillable = [
        'staff_query_id',
        'user_id',
        'comment',
    ];

    public function staffQuery(): BelongsTo
    {
        return $this->belongsTo(StaffQuery::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
