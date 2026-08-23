@extends('layouts.admin')
@section('title', 'Attendance | Printbuka')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div>
        <h1 class="text-2xl font-black text-slate-950">My Attendance</h1>
        @if ($location)
            <p class="text-sm text-slate-500 mt-1">Clock in/out from {{ $location->name }} — {{ $location->address }}.</p>
        @endif
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="pb-card p-6 text-center">
        @if (! $todayRecord || ! $todayRecord->clock_in_at)
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Not clocked in today</p>
            <p class="text-3xl font-black text-slate-950 mb-6">{{ now(config('app.business_timezone'))->format('h:i A') }}</p>

            <form method="POST" action="{{ route('admin.attendance.clock-in') }}" data-attendance-form>
                @csrf
                <label class="block mb-4">
                    <span class="pb-label">Selfie (optional, low-res)</span>
                    <input type="file" name="photo" accept="image/*" capture="user" data-attendance-photo class="pb-input">
                </label>
                <p class="text-xs text-slate-400 mb-4" data-attendance-status></p>
                <button type="submit" class="pb-btn pb-btn-primary w-full" data-original-label="Clock In">Clock In</button>
            </form>
        @elseif (! $todayRecord->clock_out_at)
            <p class="text-xs font-black uppercase tracking-wide text-emerald-600 mb-1">Clocked in at {{ $todayRecord->clock_in_at->format('h:i A') }}</p>
            <p class="text-3xl font-black text-slate-950 mb-1">{{ now(config('app.business_timezone'))->format('h:i A') }}</p>
            <p class="text-sm text-slate-500 mb-6">{{ $todayRecord->durationInMinutes() }} minutes so far</p>

            @if ($todayRecord->clock_in_within_geofence === false)
                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-bold text-amber-800">
                    You clocked in {{ $todayRecord->clock_in_distance_meters }}m from {{ $location?->name }} — flagged for review.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.attendance.clock-out') }}" data-attendance-form>
                @csrf
                <label class="block mb-4">
                    <span class="pb-label">Selfie (optional, low-res)</span>
                    <input type="file" name="photo" accept="image/*" capture="user" data-attendance-photo class="pb-input">
                </label>
                <p class="text-xs text-slate-400 mb-4" data-attendance-status></p>
                <button type="submit" class="pb-btn pb-btn-primary w-full" data-original-label="Clock Out">Clock Out</button>
            </form>
        @else
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Shift complete</p>
            <p class="text-lg font-black text-slate-950">{{ $todayRecord->clock_in_at->format('h:i A') }} – {{ $todayRecord->clock_out_at->format('h:i A') }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ $todayRecord->durationInMinutes() }} minutes worked</p>
            <div class="mt-3 flex items-center justify-center gap-2">
                <span class="pb-badge {{ $todayRecord->statusBadgeClass() }}">{{ $todayRecord->statusLabel() }}</span>
                @if ($todayRecord->hasOvertime())
                    <span class="pb-badge bg-purple-100 text-purple-800">+{{ $todayRecord->overtimeLabel() }} overtime</span>
                @endif
            </div>
        @endif
    </div>

    <div class="pb-card p-6">
        <p class="text-sm font-black text-slate-900 mb-4">Recent history</p>
        <div class="space-y-2">
            @forelse ($recent as $record)
                <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($record->work_date)->format('D, M j') }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $record->clock_in_at?->format('h:i A') ?? '—' }} – {{ $record->clock_out_at?->format('h:i A') ?? '—' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="pb-badge {{ $record->statusBadgeClass() }}">{{ $record->statusLabel() }}</span>
                        @if ($record->hasOvertime())
                            <span class="pb-badge bg-purple-100 text-purple-800 text-[10px]">+{{ $record->overtimeLabel() }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">No attendance history yet.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
