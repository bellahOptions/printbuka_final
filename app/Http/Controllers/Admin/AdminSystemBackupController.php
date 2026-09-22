<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemBackup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Full system backups (database dump + storage/app files), zipped by
 * spatie/laravel-backup and tracked in system_backups. Locked to the literal
 * super_admin role via the 'super.admin' route middleware — not just the
 * system_backups.manage permission — since this exposes the entire
 * database and every uploaded file in one download.
 */
class AdminSystemBackupController extends Controller
{
    public function index(): View
    {
        $backups = SystemBackup::query()
            ->with('triggeredBy')
            ->latest('started_at')
            ->paginate(20);

        $lastSuccess = SystemBackup::query()
            ->where('status', SystemBackup::STATUS_SUCCESS)
            ->latest('completed_at')
            ->first();

        $stats = [
            'total' => SystemBackup::count(),
            'successful' => SystemBackup::where('status', SystemBackup::STATUS_SUCCESS)->count(),
            'failed' => SystemBackup::where('status', SystemBackup::STATUS_FAILED)->count(),
            'total_size_bytes' => (int) SystemBackup::where('status', SystemBackup::STATUS_SUCCESS)->sum('size_bytes'),
            'last_success' => $lastSuccess,
            'is_running' => SystemBackup::running()->exists(),
        ];

        $diskFreeBytes = $this->diskFreeBytes();

        return view('admin.system-backups.index', [
            'backups' => $backups,
            'stats' => $stats,
            'diskFreeBytes' => $diskFreeBytes,
            'backupDisk' => (string) (config('backup.backup.destination.disks')[0] ?? 'backups'),
        ]);
    }

    public function run(Request $request): RedirectResponse
    {
        if (SystemBackup::running()->exists()) {
            return back()->with('error', 'A backup is already running — wait for it to finish before starting another.');
        }

        $backup = SystemBackup::create([
            'type' => 'manual',
            'status' => SystemBackup::STATUS_RUNNING,
            'disk' => (string) (config('backup.backup.destination.disks')[0] ?? 'backups'),
            'triggered_by' => $request->user()->id,
            'started_at' => now(),
        ]);

        // Run detached rather than inline: a full backup (DB dump + zipping
        // every application file) can take minutes, far longer than is safe
        // to hold an HTTP request/web server worker open for, and this app
        // has no queue worker guaranteed to be running in production.
        Process::path(base_path())
            ->timeout(0)
            ->start([PHP_BINARY, base_path('artisan'), 'system-backup:run', '--id='.$backup->id]);

        return redirect()->route('admin.system-backups.index')
            ->with('status', 'Backup started in the background — this page will show it as complete in a minute or two. Refresh to check progress.');
    }

    public function download(SystemBackup $systemBackup): StreamedResponse|RedirectResponse
    {
        if (! $systemBackup->isDownloadable() || ! Storage::disk($systemBackup->disk)->exists($systemBackup->path)) {
            return back()->with('error', 'That backup file is no longer available.');
        }

        return Storage::disk($systemBackup->disk)->download($systemBackup->path, basename($systemBackup->path));
    }

    public function destroy(SystemBackup $systemBackup): RedirectResponse
    {
        if ($systemBackup->isRunning()) {
            return back()->with('error', 'This backup is still running and cannot be deleted yet.');
        }

        if (filled($systemBackup->path)) {
            Storage::disk($systemBackup->disk)->delete($systemBackup->path);
        }

        $systemBackup->delete();

        return back()->with('status', 'Backup deleted.');
    }

    private function diskFreeBytes(): ?int
    {
        try {
            $path = Storage::disk((string) (config('backup.backup.destination.disks')[0] ?? 'backups'))->path('');
            $free = @disk_free_space($path);

            return $free === false ? null : (int) $free;
        } catch (\Throwable) {
            return null;
        }
    }
}
