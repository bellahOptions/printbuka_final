<?php

namespace App\Models;

use App\Models\Concerns\RoutesByUuid;
use App\Support\FileSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An audit-trail row for one backup run (manual or scheduled). The actual
 * zip archive lives on the `disk` this row names — see
 * App\Console\Commands\RunSystemBackup, which is the only thing that writes
 * these rows.
 */
class SystemBackup extends Model
{
    use RoutesByUuid;

    public const STATUS_RUNNING = 'running';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'type', 'status', 'disk', 'path', 'size_bytes', 'error_message',
        'triggered_by', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function scopeRunning(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_RUNNING);
    }

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    public function isDownloadable(): bool
    {
        return $this->status === self::STATUS_SUCCESS && filled($this->path);
    }

    /**
     * How long the run took (or has been running for), for display.
     */
    public function duration(): ?string
    {
        if ($this->started_at === null) {
            return null;
        }

        $end = $this->completed_at ?? now();

        return $this->started_at->diffForHumans($end, ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2]);
    }

    public function formattedSize(): string
    {
        return FileSize::format($this->size_bytes);
    }
}
