<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        if (! filled($this->featured_image)) {
            return null;
        }

        if (filter_var($this->featured_image, FILTER_VALIDATE_URL)) {
            return (string) $this->featured_image;
        }

        return Storage::disk('public')->url((string) $this->featured_image);
    }

    /**
     * @return array<int, string>
     */
    public function additionalImageUrls(): array
    {
        return collect((array) $this->additional_images)
            ->filter(fn ($path): bool => filled($path))
            ->map(function (string $path): string {
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    return $path;
                }

                return Storage::disk('public')->url($path);
            })
            ->values()
            ->all();
    }
}
