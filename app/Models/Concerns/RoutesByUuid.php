<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Routes this model by an opaque `uuid` column instead of its (still
 * auto-increment) primary key, so URLs never expose a sequential,
 * enumerable ID. The `id` column remains the real primary key and every
 * foreign key/relationship is untouched — this only changes what
 * `getRouteKeyName()` resolves for implicit route-model binding and what
 * `route()` embeds when a model instance is passed to it.
 */
trait RoutesByUuid
{
    public static function bootRoutesByUuid(): void
    {
        static::creating(function ($model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
