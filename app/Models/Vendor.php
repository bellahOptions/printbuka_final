<?php

namespace App\Models;

use App\Models\Concerns\RoutesByUuid;
use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes, RoutesByUuid;

    /**
     * @var array<int, string>
     */
    public const VENDOR_TYPES = [
        'Supplier', 'Engineer', 'Contractor', 'Service Provider', 'Consultant', 'Freelancer', 'Other',
    ];

    /**
     * @var array<int, string>
     */
    public const STATUSES = ['active', 'inactive', 'blacklisted'];

    protected $fillable = [
        'code', 'code_sequence', 'name', 'company_name', 'vendor_type', 'category',
        'contact_person', 'email', 'phone', 'alternate_phone', 'website',
        'address', 'city', 'state', 'country', 'tax_id',
        'bank_name', 'bank_account_name', 'bank_account_number',
        'rating', 'status', 'tags', 'logo', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Auto-generate a vendor code after the record is persisted so we
        // have the ID. Format: VDR-YYYY-NNNNN e.g. VDR-2026-00003
        static::created(function (Vendor $vendor): void {
            if (filled($vendor->code)) {
                return;
            }

            $year     = now()->format('Y');
            $sequence = static::max('code_sequence') ?? 0;
            $sequence++;

            $code = 'VDR-'.$year.'-'.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);

            $vendor->updateQuietly(['code' => $code, 'code_sequence' => $sequence]);
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    public function scopeBlacklisted(Builder $query): Builder
    {
        return $query->where('status', 'blacklisted');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('vendor_type', $type);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('company_name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('contact_person', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('alternate_phone', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%")
                ->orWhere('tags', 'like', "%{$term}%");
        });
    }

    /** @return array<int, string> */
    public function tagList(): array
    {
        return collect(explode(',', (string) $this->tags))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBlacklisted(): bool
    {
        return $this->status === 'blacklisted';
    }

    public function displayName(): string
    {
        return $this->company_name ?: $this->name;
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->displayName()));
        $words = array_filter((array) $words);

        if ($words === []) {
            return '?';
        }

        $initials = array_map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)), $words);

        return implode('', array_slice($initials, 0, 2));
    }

    public function logoUrl(): ?string
    {
        return MediaUrl::resolve($this->logo);
    }
}
