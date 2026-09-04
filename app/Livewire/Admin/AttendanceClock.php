<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceRecord;
use App\Models\WorkLocation;
use App\Services\CloudinaryUploadService;
use App\Support\AttendanceCalculator;
use App\Support\SiteSettings;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class AttendanceClock extends Component
{
    use WithFileUploads;

    public $photo = null;

    public ?string $statusMessage = null;

    /**
     * Clock in — hard-blocked unless the staff member's device reports a GPS
     * fix that falls within the active office geofence. Unlike clock-out,
     * there is no "flag and allow" fallback here: no location, or a location
     * outside the radius, means no clock-in.
     */
    public function clockIn(?float $lat = null, ?float $lng = null, ?int $accuracyMeters = null): void
    {
        $this->statusMessage = null;
        $this->resetErrorBag();

        $user = auth()->user();
        $today = $this->today();

        $existing = AttendanceRecord::query()->where('user_id', $user->id)->where('work_date', $today)->first();
        if ($existing && $existing->clock_in_at) {
            $this->addError('clockIn', 'You have already clocked in today.');

            return;
        }

        $this->validate(['photo' => ['nullable', 'image', 'max:2048']]);

        [$lat, $lng, $accuracyMeters] = $this->sanitizeCoordinates($lat, $lng, $accuracyMeters);

        $location = $this->activeLocation();
        $expectedOnsite = $this->isExpectedOnsiteToday($user);

        if ($expectedOnsite) {
            if (! $location || ! $location->hasCoordinates()) {
                $this->addError('clockIn', 'No office location is configured yet — ask an administrator to set one before clocking in.');

                return;
            }

            if ($lat === null || $lng === null) {
                $this->addError('clockIn', 'We could not verify your location. Please enable location services on your device and try again.');

                return;
            }

            $distance = $location->distanceToInMeters($lat, $lng);
            $effectiveDistance = $this->effectiveDistance($distance, $accuracyMeters);

            if ($distance === null || $effectiveDistance > $location->radius_meters) {
                $accuracyNote = $accuracyMeters !== null ? " (device location accuracy: ±{$accuracyMeters}m)" : '';
                $this->addError('clockIn', "You're {$distance}m from {$location->name}{$accuracyNote} — you need to be within {$location->radius_meters}m of the office to clock in.");

                return;
            }
        } else {
            // Remote (or hybrid, on a declared remote day) staff aren't held
            // to the office geofence at all — still record whatever location
            // fix is available, purely for reference, without blocking.
            $distance = ($location && $location->hasCoordinates() && $lat !== null && $lng !== null)
                ? $location->distanceToInMeters($lat, $lng)
                : null;
        }

        $photoPath = $this->storePhoto();
        $now = now(config('app.business_timezone'));

        $record = AttendanceRecord::query()->updateOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            [
                'work_location_id' => $location?->id,
                'clock_in_at' => $now,
                'clock_in_lat' => $lat,
                'clock_in_lng' => $lng,
                'clock_in_distance_meters' => $distance,
                'clock_in_accuracy_meters' => $accuracyMeters,
                'clock_in_within_geofence' => $expectedOnsite ? true : null,
                'clock_in_photo_path' => $photoPath,
                'status' => $this->resolveClockInStatus($now),
                'flagged_reason' => null,
            ]
        );

        $this->reset('photo');

        $this->statusMessage = $record->status === 'late'
            ? 'Clocked in — marked late for today.'
            : 'Clocked in. Have a great shift!';
    }

    /**
     * Clock out is only allowed off-site with a flag — staff may legitimately
     * be out on a delivery run or errand at the end of a shift, so location
     * is recorded and compared but never blocks the punch itself.
     */
    public function clockOut(?float $lat = null, ?float $lng = null, ?int $accuracyMeters = null): void
    {
        $this->statusMessage = null;
        $this->resetErrorBag();

        $user = auth()->user();
        $today = $this->today();

        $record = AttendanceRecord::query()->where('user_id', $user->id)->where('work_date', $today)->first();

        if (! $record || ! $record->clock_in_at) {
            $this->addError('clockOut', 'You have not clocked in today.');

            return;
        }

        if ($record->clock_out_at) {
            $this->addError('clockOut', 'You have already clocked out today.');

            return;
        }

        $this->validate(['photo' => ['nullable', 'image', 'max:2048']]);

        [$lat, $lng, $accuracyMeters] = $this->sanitizeCoordinates($lat, $lng, $accuracyMeters);

        $location = $record->workLocation ?? $this->activeLocation();
        $distance = ($location && $lat !== null && $lng !== null)
            ? $location->distanceToInMeters($lat, $lng)
            : null;
        $effectiveDistance = $this->effectiveDistance($distance, $accuracyMeters);

        $photoPath = $this->storePhoto();
        $clockOutAt = now(config('app.business_timezone'));
        $overtimeMinutes = $this->calculateOvertimeMinutes($clockOutAt);

        $record->update([
            'clock_out_at' => $clockOutAt,
            'clock_out_lat' => $lat,
            'clock_out_lng' => $lng,
            'clock_out_distance_meters' => $distance,
            'clock_out_accuracy_meters' => $accuracyMeters,
            'clock_out_within_geofence' => $location && $effectiveDistance !== null ? $effectiveDistance <= $location->radius_meters : null,
            'clock_out_photo_path' => $photoPath,
            'overtime_minutes' => $overtimeMinutes,
        ]);

        $this->reset('photo');

        $this->statusMessage = $overtimeMinutes > 0
            ? 'Clocked out — '.$record->fresh()->overtimeLabel().' of overtime recorded today.'
            : 'Clocked out. See you next shift!';
    }

    public function render()
    {
        $userId = auth()->id();
        $today = $this->today();

        return view('livewire.admin.attendance-clock', [
            'todayRecord' => AttendanceRecord::query()
                ->where('user_id', $userId)
                ->where('work_date', $today)
                ->first(),
            'recent' => AttendanceRecord::query()
                ->where('user_id', $userId)
                ->orderByDesc('work_date')
                ->limit(14)
                ->get(),
            'location' => $this->activeLocation(),
            'expectedOnsiteToday' => $this->isExpectedOnsiteToday(auth()->user()),
        ]);
    }

    private function today(): string
    {
        return now(config('app.business_timezone'))->toDateString();
    }

    private function activeLocation(): ?WorkLocation
    {
        return WorkLocation::query()->where('is_active', true)->first();
    }

    private function isExpectedOnsiteToday(\App\Models\User $user): bool
    {
        return ($user->staffProfile?->isExpectedOnsite(now(config('app.business_timezone'))) ?? true);
    }

    /**
     * Discounts the reported distance by the device's GPS accuracy radius —
     * a low-confidence fix (common indoors, where phones fall back to
     * network/WiFi positioning) can be off by hundreds of meters even when
     * the device is physically on-site, so the true position could be
     * anywhere within that accuracy circle.
     */
    private function effectiveDistance(?int $distance, ?int $accuracyMeters): ?int
    {
        if ($distance === null) {
            return null;
        }

        return $accuracyMeters !== null ? max(0, $distance - $accuracyMeters) : $distance;
    }

    /**
     * @return array{0: ?float, 1: ?float, 2: ?int}
     */
    private function sanitizeCoordinates(?float $lat, ?float $lng, ?int $accuracyMeters): array
    {
        if ($lat === null || $lng === null || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return [null, null, null];
        }

        return [$lat, $lng, $accuracyMeters !== null ? max(0, $accuracyMeters) : null];
    }

    private function resolveClockInStatus(Carbon $clockInAt): string
    {
        $settings = SiteSettings::all();
        $shiftStart = (string) ($settings['attendance_shift_start'] ?? '08:00');
        $graceMinutes = (int) ($settings['attendance_late_grace_minutes'] ?? 15);

        $expected = $clockInAt->copy()->setTimeFromTimeString($shiftStart)->addMinutes($graceMinutes);

        return $clockInAt->greaterThan($expected) ? 'late' : 'present';
    }

    /**
     * Only counts time on the SAME calendar day as shift end (an auto-closed,
     * forgotten clock-out is never awarded overtime — see AttendanceProcessingService).
     */
    private function calculateOvertimeMinutes(Carbon $clockOutAt): int
    {
        return AttendanceCalculator::overtimeMinutes($clockOutAt);
    }

    private function storePhoto(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        $result = app(CloudinaryUploadService::class)->storeToBoth(
            $this->photo,
            'attendance-photos',
            'attendance-photos'
        );

        return $result['cloudinary_public_id'] ?? $result['path'] ?? null;
    }
}
