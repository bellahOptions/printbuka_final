<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffQuery extends Model
{
    public static array $types = [
        'verbal_warning',
        'written_warning',
        'final_warning',
        'suspension_notice',
        'query_letter',
        'misconduct',
        'performance',
        'attendance',
        'other',
    ];

    protected $fillable = [
        'staff_id', 'issued_by_id', 'query_number', 'query_date', 'query_type',
        'subject', 'description', 'cc_emails', 'bcc_emails', 'response_due_date',
        'staff_response', 'staff_responded_at', 'status',
        'resolved_by_id', 'resolved_at', 'resolution_notes',
        'email_last_sent_at', 'email_send_count',
    ];

    protected function casts(): array
    {
        return [
            'query_date'          => 'date',
            'response_due_date'   => 'date',
            'staff_responded_at'  => 'datetime',
            'resolved_at'         => 'datetime',
            'email_last_sent_at'  => 'datetime',
        ];
    }

    /**
     * Parse a free-form comma/semicolon/newline-separated string of addresses
     * into a clean list of valid, deduplicated emails.
     *
     * @return array<int, string>
     */
    public static function parseEmailList(?string $raw): array
    {
        if (! filled($raw)) {
            return [];
        }

        return collect(preg_split('/[,;\n]+/', $raw) ?: [])
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->unique()
            ->values()
            ->all();
    }

    public function ccList(): array
    {
        return self::parseEmailList($this->cc_emails);
    }

    public function bccList(): array
    {
        return self::parseEmailList($this->bcc_emails);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(StaffQueryComment::class)->oldest();
    }

    public function typeLabel(): string
    {
        return match ($this->query_type) {
            'verbal_warning'      => 'Verbal Warning',
            'written_warning'     => 'Written Warning',
            'query'               => 'Formal Query',
            'suspension_notice'   => 'Suspension Notice',
            'termination_notice'  => 'Termination Notice',
            default               => ucwords(str_replace('_', ' ', (string) $this->query_type)),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending'    => 'bg-amber-100 text-amber-800',
            'responded'  => 'bg-blue-100 text-blue-800',
            'closed'     => 'bg-emerald-100 text-emerald-800',
            'escalated'  => 'bg-pink-100 text-pink-800',
            default      => 'bg-slate-100 text-slate-700',
        };
    }
}
