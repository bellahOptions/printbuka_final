<?php

namespace App\Console\Commands;

use App\Models\SystemBackup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Backup\BackupDestination\BackupDestination;

/**
 * Thin wrapper around spatie/laravel-backup's own `backup:run` command that
 * records the outcome in `system_backups` so Super Admin has an in-app
 * history/audit trail (who triggered it, how long it took, how big it was)
 * instead of only a raw file listing on disk.
 *
 * Used two ways:
 *  - the daily cron (see routes/console.php), with no --id — it creates its
 *    own row (type=scheduled).
 *  - the admin "Run Backup Now" button, which pre-creates a `running` row
 *    synchronously (so the UI can show it immediately) then launches this
 *    command in a detached process and passes that row's --id.
 */
class RunSystemBackup extends Command
{
    protected $signature = 'system-backup:run {--id= : An existing system_backups row to update instead of creating a new one} {--type=scheduled : manual|scheduled, only used when --id is omitted}';

    protected $description = 'Run a full database + files backup and record the result in system_backups.';

    public function handle(): int
    {
        $this->failStaleRunningRows();

        $backup = $this->resolveBackupRow();

        if ($backup === null) {
            $this->error("No system_backups row found for --id={$this->option('id')}.");

            return self::FAILURE;
        }

        $disk = (string) (config('backup.backup.destination.disks')[0] ?? 'backups');
        $backupName = (string) config('backup.backup.name');

        $this->info('Starting backup (database + application files)...');

        // backup:run can throw after the archive is already safely written —
        // e.g. a notification failing to send over a flaky mail transport.
        // That must not misreport a real, usable backup as failed, so the
        // actual signal of success is "did a new file land on disk", not
        // "did the artisan process exit cleanly".
        try {
            $exitCode = Artisan::call('backup:run', ['--disable-notifications' => false]);
            $output = trim(Artisan::output());
        } catch (\Throwable $e) {
            report($e);
            $exitCode = 1;
            $output = 'backup:run threw: '.$e->getMessage();
        }
        $this->line($output);

        $newest = BackupDestination::create($disk, $backupName)->newestBackup();

        // Only trust a backup file that was written after this run started —
        // otherwise a failed run with a stale existing backup on disk would
        // be misreported as a success.
        $producedFile = $newest !== null && $newest->date()->gte($backup->started_at)
            ? $newest
            : null;

        if ($producedFile !== null) {
            $backup->update([
                'status' => SystemBackup::STATUS_SUCCESS,
                'path' => $producedFile->path(),
                'size_bytes' => (int) $producedFile->sizeInBytes(),
                'completed_at' => now(),
            ]);
            $this->info('Backup completed: '.$producedFile->path());

            return self::SUCCESS;
        }

        $backup->update([
            'status' => SystemBackup::STATUS_FAILED,
            'error_message' => $output !== '' ? mb_substr($output, -4000) : "backup:run exited with code {$exitCode}.",
            'completed_at' => now(),
        ]);
        $this->error('Backup failed — see the error recorded on the system_backups row.');

        return self::FAILURE;
    }

    /**
     * A row can be stuck at status=running forever if the PHP process behind
     * it was killed outright (OOM, deploy restart, server crash) before it
     * ever reached the try/catch in handle() — nothing would ever mark it
     * failed. Sweep those up on every run so the admin UI's spinner can't
     * lie indefinitely.
     */
    private function failStaleRunningRows(): void
    {
        SystemBackup::query()
            ->running()
            ->where('started_at', '<', now()->subHours(2))
            ->update([
                'status' => SystemBackup::STATUS_FAILED,
                'error_message' => 'Marked failed automatically: still "running" after 2 hours, the process likely crashed or was killed.',
                'completed_at' => now(),
            ]);
    }

    private function resolveBackupRow(): ?SystemBackup
    {
        if ($this->option('id')) {
            return SystemBackup::find($this->option('id'));
        }

        $type = in_array($this->option('type'), ['manual', 'scheduled'], true) ? $this->option('type') : 'scheduled';

        return SystemBackup::create([
            'type' => $type,
            'status' => SystemBackup::STATUS_RUNNING,
            'disk' => (string) (config('backup.backup.destination.disks')[0] ?? 'backups'),
            'started_at' => now(),
        ]);
    }
}
