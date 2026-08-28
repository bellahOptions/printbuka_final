<div id="pb-attendance-clock">

    @if ($statusMessage)
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ $statusMessage }}</div>
    @endif

    <div class="pb-card p-6 text-center">
        @if (! $todayRecord || ! $todayRecord->clock_in_at)
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Not clocked in today</p>
            <p class="text-3xl font-black text-slate-950 mb-6">{{ now(config('app.business_timezone'))->format('h:i A') }}</p>

            @if ($expectedOnsiteToday)
                @if ($location)
                    <p class="mb-4 text-xs text-slate-400">
                        You must be within {{ $location->radius_meters }}m of {{ $location->name }} to clock in — we'll check your device's GPS location when you tap the button below.
                    </p>
                @endif
            @else
                <p class="mb-4 rounded-xl border border-cyan-200 bg-cyan-50 p-3 text-xs font-bold text-cyan-800">
                    You're set as working remotely today — no office check-in required.
                </p>
            @endif

            <label class="block mb-4 text-left">
                <span class="pb-label">Selfie (optional, low-res)</span>
                <input type="file" accept="image/*" capture="user" data-attendance-photo class="pb-input">
            </label>

            @error('clockIn')
                <p class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-bold text-red-800">{{ $message }}</p>
            @enderror
            @error('photo')
                <p class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-bold text-red-800">{{ $message }}</p>
            @enderror

            <p class="text-xs text-slate-400 mb-4" data-attendance-status></p>

            <button type="button" data-attendance-punch="clockIn" data-original-label="Clock In" class="pb-btn pb-btn-primary w-full">Clock In</button>
        @elseif (! $todayRecord->clock_out_at)
            <p class="text-xs font-black uppercase tracking-wide text-emerald-600 mb-1">Clocked in at {{ $todayRecord->clock_in_at->format('h:i A') }}</p>
            <p class="text-3xl font-black text-slate-950 mb-1">{{ now(config('app.business_timezone'))->format('h:i A') }}</p>
            <p class="text-sm text-slate-500 mb-6">{{ $todayRecord->durationInMinutes() }} minutes so far</p>

            <label class="block mb-4 text-left">
                <span class="pb-label">Selfie (optional, low-res)</span>
                <input type="file" accept="image/*" capture="user" data-attendance-photo class="pb-input">
            </label>

            @error('clockOut')
                <p class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-bold text-red-800">{{ $message }}</p>
            @enderror
            @error('photo')
                <p class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-bold text-red-800">{{ $message }}</p>
            @enderror

            <p class="text-xs text-slate-400 mb-4" data-attendance-status></p>

            <button type="button" data-attendance-punch="clockOut" data-original-label="Clock Out" class="pb-btn pb-btn-primary w-full">Clock Out</button>
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
            @if ($todayRecord->clock_out_within_geofence === false)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-bold text-amber-800">
                    You clocked out {{ $todayRecord->clock_out_distance_meters }}m from {{ $location?->name }} — flagged for review.
                </div>
            @endif
        @endif
    </div>

    <div class="pb-card p-6 mt-6">
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

    @script
    <script>
        (() => {
            const root = document.getElementById('pb-attendance-clock');
            if (!root || root.dataset.pbAttendanceBound) return;
            root.dataset.pbAttendanceBound = '1';

            const MAX_PHOTO_WIDTH = 480;
            const PHOTO_QUALITY = 0.6;

            const downscalePhoto = (file) => new Promise((resolve, reject) => {
                const img = new Image();
                const url = URL.createObjectURL(file);

                img.onload = () => {
                    const scale = Math.min(1, MAX_PHOTO_WIDTH / img.width);
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(img.width * scale);
                    canvas.height = Math.round(img.height * scale);

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    URL.revokeObjectURL(url);

                    canvas.toBlob((blob) => (blob ? resolve(blob) : reject(new Error('Could not process photo.'))), 'image/jpeg', PHOTO_QUALITY);
                };
                img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('Could not read photo.')); };
                img.src = url;
            });

            const toLocationResult = (position) => ({
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                accuracy: Number.isFinite(position.coords.accuracy) ? Math.round(position.coords.accuracy) : null,
            });

            const requestPosition = (options) => new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, options);
            });

            const getLocation = async () => {
                // Inside the Capacitor shell, the native Geolocation plugin handles OS
                // permission prompts more reliably than the raw browser API embedded
                // in a WebView. Fall back to the browser API everywhere else (plain
                // mobile/desktop browser, or if the plugin isn't present).
                if (window.pbNativeGeolocation) {
                    return window.pbNativeGeolocation();
                }

                if (!navigator.geolocation) return null;

                try {
                    // First try: a fresh, high-accuracy GPS fix — best case outdoors.
                    return toLocationResult(await requestPosition({ enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 }));
                } catch {
                    // GPS often can't get a satellite lock indoors (e.g. inside the
                    // office itself) and times out. Fall back to network/WiFi-based
                    // positioning, which is faster and works far better indoors, at
                    // the cost of some precision — still far better than failing
                    // the clock-in outright for staff who are genuinely on site.
                    try {
                        return toLocationResult(await requestPosition({ enableHighAccuracy: false, timeout: 10000, maximumAge: 120000 }));
                    } catch {
                        return null; // permission denied, or truly no fix available
                    }
                }
            };

            let busy = false;

            root.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-attendance-punch]');
                if (!button || busy) return;

                event.preventDefault();
                busy = true;

                const action = button.dataset.attendancePunch;
                const statusEl = root.querySelector('[data-attendance-status]');
                const originalLabel = button.dataset.originalLabel || button.textContent;

                button.disabled = true;
                button.textContent = 'Getting location…';
                if (statusEl) statusEl.textContent = 'Getting your location…';

                try {
                    const loc = await getLocation();

                    const photoInput = root.querySelector('[data-attendance-photo]');
                    let photoFile = photoInput?.files?.[0] ?? null;

                    if (photoFile) {
                        button.textContent = 'Processing photo…';
                        if (statusEl) statusEl.textContent = 'Processing photo…';
                        try {
                            const downscaled = await downscalePhoto(photoFile);
                            photoFile = new File([downscaled], 'attendance.jpg', { type: 'image/jpeg' });
                        } catch {
                            // Fall back to the original, unprocessed photo.
                        }
                    }

                    button.textContent = 'Submitting…';
                    if (statusEl) statusEl.textContent = 'Submitting…';

                    if (photoFile) {
                        await new Promise((resolve, reject) => {
                            $wire.upload('photo', photoFile, resolve, reject);
                        });
                    }

                    await $wire.call(action, loc?.lat ?? null, loc?.lng ?? null, loc?.accuracy ?? null);
                } catch (error) {
                    if (statusEl) statusEl.textContent = 'Something went wrong — please try again.';
                } finally {
                    busy = false;
                    if (document.body.contains(button)) {
                        button.disabled = false;
                        button.textContent = originalLabel;
                    }
                }
            });
        })();
    </script>
    @endscript
</div>
