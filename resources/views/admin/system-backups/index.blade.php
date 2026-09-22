@extends('layouts.admin')
@section('title', 'System Backups')
@section('content')

<div class="pb-page-header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="pb-page-title text-xl font-black mb-2">System Backups</h1>
            <p class="text-sm text-slate-400">Full database + file backups, zipped and stored on this server. Super Admin only.</p>
        </div>
        <form method="POST" action="{{ route('admin.system-backups.run') }}">
            @csrf
            <button type="submit" class="pb-btn pb-btn-md pb-btn-primary self-start" {{ $stats['is_running'] ? 'disabled' : '' }}>
                <x-heroicon-o-server-stack class="w-4 h-4" />
                {{ $stats['is_running'] ? 'Backup running…' : 'Run Backup Now' }}
            </button>
        </form>
    </div>
</div>

@if(session('status'))
    <div class="pb-alert pb-alert-success mb-5">
        <x-heroicon-o-check-circle class="w-5 h-5" /> {{ session('status') }}
    </div>
@endif

@if(session('error'))
    <div class="pb-alert pb-alert-danger mb-5">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5" /> {{ session('error') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="pb-card p-4">
        <p class="text-xs font-bold uppercase text-slate-400">Total Backups</p>
        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="pb-card p-4">
        <p class="text-xs font-bold uppercase text-emerald-600">Successful</p>
        <p class="text-2xl font-black text-slate-900">{{ number_format($stats['successful']) }}</p>
    </div>
    <div class="pb-card p-4">
        <p class="text-xs font-bold uppercase {{ $stats['failed'] > 0 ? 'text-red-600' : 'text-slate-400' }}">Failed</p>
        <p class="text-2xl font-black {{ $stats['failed'] > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ number_format($stats['failed']) }}</p>
    </div>
    <div class="pb-card p-4">
        <p class="text-xs font-bold uppercase text-slate-400">Last Successful Backup</p>
        <p class="text-sm font-black text-slate-900" title="{{ $stats['last_success']?->completed_at }}">
            {{ $stats['last_success']?->completed_at?->diffForHumans() ?? 'Never' }}
        </p>
    </div>
</div>

<div class="pb-card p-4 mb-6 flex flex-wrap items-center justify-between gap-3 text-sm">
    <div class="text-slate-600">
        <span class="font-bold text-slate-900">{{ \App\Support\FileSize::format($stats['total_size_bytes']) }}</span> used across all stored backups on the
        <span class="font-mono">{{ $backupDisk }}</span> disk.
    </div>
    @if($diskFreeBytes !== null)
        <div class="text-slate-600">
            <span class="font-bold text-slate-900">{{ \App\Support\FileSize::format($diskFreeBytes) }}</span> free on this server's disk.
        </div>
    @endif
</div>

<div class="pb-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="pb-table w-full">
            <thead>
                <tr>
                    <th>Started</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Duration</th>
                    <th>Size</th>
                    <th>Triggered By</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                    <tr>
                        <td>
                            <p class="font-bold text-slate-900" title="{{ $backup->started_at }}">{{ $backup->started_at->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-slate-400">{{ $backup->started_at->diffForHumans() }}</p>
                        </td>
                        <td>
                            <span class="pb-badge pb-badge-secondary capitalize">{{ $backup->type }}</span>
                        </td>
                        <td>
                            @if($backup->status === 'success')
                                <span class="pb-badge pb-badge-success">Success</span>
                            @elseif($backup->status === 'failed')
                                <span class="pb-badge pb-badge-danger">Failed</span>
                            @else
                                <span class="pb-badge pb-badge-info">Running…</span>
                            @endif
                            @if($backup->status === 'failed' && $backup->error_message)
                                <p class="text-xs text-red-600 mt-1 max-w-xs truncate" title="{{ $backup->error_message }}">{{ $backup->error_message }}</p>
                            @endif
                        </td>
                        <td class="text-sm text-slate-600">{{ $backup->duration() ?: '—' }}</td>
                        <td class="text-sm text-slate-600">{{ $backup->formattedSize() }}</td>
                        <td class="text-sm text-slate-600">{{ $backup->triggeredBy?->displayName() ?: ($backup->type === 'scheduled' ? 'Automatic (cron)' : '—') }}</td>
                        <td class="text-right whitespace-nowrap">
                            @if($backup->isDownloadable())
                                <a href="{{ route('admin.system-backups.download', $backup) }}" class="pb-btn pb-btn-sm pb-btn-ghost">
                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Download
                                </a>
                            @endif
                            @if(!$backup->isRunning())
                                <form method="POST" action="{{ route('admin.system-backups.destroy', $backup) }}" class="inline"
                                      onsubmit="return confirm('Delete this backup? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pb-btn pb-btn-sm pb-btn-ghost text-red-600">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="pb-empty">No backups yet — click "Run Backup Now" to create the first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $backups->links() }}
</div>

@if($stats['is_running'])
    <script>
        // A backup is running — reload periodically so the row flips to
        // success/failed without the admin needing to manually refresh.
        setTimeout(() => window.location.reload(), 8000);
    </script>
@endif

@endsection
