<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffActivity extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'department',
        'action',
        'subject_type',
        'subject_id',
        'ip_address',
        'route_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
