@extends('layouts.admin')
@section('title', 'Attendance Location & Shift | Printbuka')

@section('content')
<div class="mx-auto max-w-xl space-y-6">

    <div>
        <a href="{{ route('admin.attendance.team') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Team Attendance</a>
        <h1 class="text-2xl font-black text-slate-950 mt-2">Location & Shift Settings</h1>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="pb-card p-6">
        <label class="pb-label">Office address (locked)</label>
        <p class="pb-input bg-slate-50 text-slate-500">{{ $location->address }}</p>
        <p class="text-xs text-slate-400 mt-2">The address is fixed. Only the precise GPS point and geofence radius below are adjustable.</p>
    </div>

    <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4 text-sm font-bold text-cyan-800">
        Attendance is mandatory Monday–Saturday. Sundays and the holidays listed below are excluded — nobody is marked absent on those days.
    </div>

    <form method="POST" action="{{ route('admin.attendance.location.update', $location) }}" class="pb-card p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="pb-label">GPS coordinates</label>
                <button type="button" id="capture-location-btn" class="text-xs font-black text-pink-600 hover:text-pink-800">Use my current location</button>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="number" step="0.0000001" name="latitude" id="latitude-input" value="{{ old('latitude', $location->latitude) }}" placeholder="Latitude" class="pb-input" required>
                <input type="number" step="0.0000001" name="longitude" id="longitude-input" value="{{ old('longitude', $location->longitude) }}" placeholder="Longitude" class="pb-input" required>
            </div>
            <p class="text-xs text-slate-400 mt-2" id="capture-status">
                Tip: stand at the workshop entrance and click "Use my current location" for the most accurate fix.
            </p>
        </div>

        <div class="pb-field">
            <label class="pb-label">Geofence radius (meters)</label>
            <input type="number" min="20" max="2000" name="radius_meters" value="{{ old('radius_meters', $location->radius_meters) }}" class="pb-input" required>
            <p class="text-xs text-slate-400 mt-1">Staff outside this radius cannot clock in. Clock-outs outside it are still accepted, but flagged for review.</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="pb-field">
                <label class="pb-label">Shift start</label>
                <input type="time" name="shift_start" value="{{ old('shift_start', $shiftStart) }}" class="pb-input" required>
            </div>
            <div class="pb-field">
                <label class="pb-label">Shift end</label>
                <input type="time" name="shift_end" value="{{ old('shift_end', $shiftEnd) }}" class="pb-input" required>
            </div>
        </div>

        <div class="pb-field">
            <label class="pb-label">Late grace period (minutes)</label>
            <input type="number" min="0" max="120" name="late_grace_minutes" value="{{ old('late_grace_minutes', $graceMinutes) }}" class="pb-input" required>
            <p class="text-xs text-slate-400 mt-1">Clock-ins after shift start + this grace period are marked Late.</p>
        </div>

        <button type="submit" class="pb-btn pb-btn-primary w-full">Save settings</button>
    </form>

    <div class="pb-card p-6">
        <p class="text-sm font-black text-slate-900 mb-1">Holidays</p>
        <p class="text-xs text-slate-500 mb-4">Dates when attendance is not required — no one is marked absent, and staff who don't clock in aren't flagged.</p>

        <div class="space-y-2 mb-5">
            @forelse ($holidays as $holiday)
                <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-2.5">
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $holiday->name }}</p>
                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($holiday->date)->format('l, F j, Y') }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.attendance.holidays.destroy', $holiday) }}" onsubmit="return confirm('Remove {{ $holiday->name }} from the holiday list?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-black text-slate-400 hover:text-pink-600">Remove</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-400">No holidays configured yet.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('admin.attendance.holidays.store') }}" class="grid gap-3 sm:grid-cols-3">
            @csrf
            <input type="date" name="date" class="pb-input sm:col-span-1" required>
            <input type="text" name="name" placeholder="e.g. Independence Day" class="pb-input sm:col-span-1" required>
            <button type="submit" class="pb-btn pb-btn-outline sm:col-span-1">+ Add holiday</button>
        </form>
        @error('date')
            <p class="text-xs font-bold text-pink-700 mt-2">{{ $message }}</p>
        @enderror
    </div>

</div>

@push('scripts')
<script>
    document.getElementById('capture-location-btn')?.addEventListener('click', () => {
        const status = document.getElementById('capture-status');
        if (!navigator.geolocation) {
            status.textContent = 'Geolocation is not available in this browser.';
            return;
        }

        status.textContent = 'Getting your current location…';
        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('latitude-input').value = position.coords.latitude.toFixed(7);
                document.getElementById('longitude-input').value = position.coords.longitude.toFixed(7);
                status.textContent = 'Location captured (accuracy: ~' + Math.round(position.coords.accuracy) + 'm). Save to apply.';
            },
            () => { status.textContent = 'Could not get your location — check browser permissions.'; },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
</script>
@endpush
@endsection
