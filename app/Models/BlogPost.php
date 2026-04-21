<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'featured_image',
        'additional_images',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'additional_images' => 'array',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function featuredImageUrl(): ?string
    {
        return MediaUrl::resolve($this->featured_image);
    }

    /**
     * @return array<int, string>
     */
    public function additionalImageUrls(): array
    {
        return collect((array) $this->additional_images)
            ->map(fn ($path): ?string => is_string($path) ? MediaUrl::resolve($path) : null)
            ->filter(fn ($path): bool => filled($path))
            ->values()
            ->all();
    }
}
