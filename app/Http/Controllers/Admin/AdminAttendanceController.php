<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Holiday;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\WorkLocation;
use App\Services\CloudinaryUploadService;
use App\Support\SiteSettings;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $today = now(config('app.business_timezone'))->toDateString();

        $todayRecord = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        $recent = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->orderByDesc('work_date')
            ->limit(14)
            ->get();

        return view('admin.attendance.index', [
            'todayRecord' => $todayRecord,
            'recent' => $recent,
            'location' => WorkLocation::query()->where('is_active', true)->first(),
        ]);
    }

    public function clockIn(Request $request): RedirectResponse
    {
        $user = $request->user();
        $today = now(config('app.business_timezone'))->toDateString();

        $existing = AttendanceRecord::query()->where('user_id', $user->id)->where('work_date', $today)->first();
        abort_if($existing && $existing->clock_in_at, 422, 'You have already clocked in today.');

        $validated = $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $location = WorkLocation::query()->where('is_active', true)->first();
        $distance = ($location && filled($validated['lat'] ?? null))
            ? $location->distanceToInMeters((float) $validated['lat'], (float) $validated['lng'])
            : null;

        $photoPath = $this->storePhoto($request, 'photo');

        $now = now(config('app.business_timezone'));

        $record = AttendanceRecord::query()->updateOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            [
                'work_location_id' => $location?->id,
                'clock_in_at' => $now,
                'clock_in_lat' => $validated['lat'] ?? null,
                'clock_in_lng' => $validated['lng'] ?? null,
                'clock_in_distance_meters' => $distance,
                'clock_in_within_geofence' => $location && $distance !== null ? $distance <= $location->radius_meters : null,
                'clock_in_photo_path' => $photoPath,
                'status' => $this->resolveClockInStatus($now),
                'flagged_reason' => ($location && $distance !== null && $distance > $location->radius_meters)
                    ? 'Clocked in '.$distance.'m from '.$location->name.' (outside the '.$location->radius_meters.'m radius).'
                    : null,
            ]
        );

        return back()->with('status', $record->status === 'late'
            ? 'Clocked in — marked late for today.'
            : 'Clocked in. Have a great shift!');
    }

    public function clockOut(Request $request): RedirectResponse
    {
        $user = $request->user();
        $today = now(config('app.business_timezone'))->toDateString();

        $record = AttendanceRecord::query()->where('user_id', $user->id)->where('work_date', $today)->first();
        abort_if(! $record || ! $record->clock_in_at, 422, 'You have not clocked in today.');
        abort_if($record->clock_out_at, 422, 'You have already clocked out today.');

        $validated = $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $location = $record->workLocation ?? WorkLocation::query()->where('is_active', true)->first();
        $distance = ($location && filled($validated['lat'] ?? null))
            ? $location->distanceToInMeters((float) $validated['lat'], (float) $validated['lng'])
            : null;

        $photoPath = $this->storePhoto($request, 'photo');
        $clockOutAt = now(config('app.business_timezone'));
        $overtimeMinutes = $this->calculateOvertimeMinutes($clockOutAt);

        $record->update([
            'clock_out_at' => $clockOutAt,
            'clock_out_lat' => $validated['lat'] ?? null,
            'clock_out_lng' => $validated['lng'] ?? null,
            'clock_out_distance_meters' => $distance,
            'clock_out_within_geofence' => $location && $distance !== null ? $distance <= $location->radius_meters : null,
            'clock_out_photo_path' => $photoPath,
            'overtime_minutes' => $overtimeMinutes,
        ]);

        return back()->with('status', $overtimeMinutes > 0
            ? 'Clocked out — '.$record->fresh()->overtimeLabel().' of overtime recorded today.'
            : 'Clocked out. See you next shift!');
    }

    public function team(Request $request): View
    {
        $date = $request->query('date', now(config('app.business_timezone'))->toDateString());

        $staff = User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        $records = AttendanceRecord::query()
            ->where('work_date', $date)
            ->get()
            ->keyBy('user_id');

        return view('admin.attendance.team', [
            'date' => $date,
            'staff' => $staff,
            'records' => $records,
        ]);
    }

    public function show(User $staff): View
    {
        $thisMonth = now(config('app.business_timezone'))->format('Y-m');

        return view('admin.attendance.show', [
            'staff' => $staff,
            'records' => AttendanceRecord::query()
                ->where('user_id', $staff->id)
                ->orderByDesc('work_date')
                ->paginate(31),
            'overtimeThisMonthMinutes' => AttendanceRecord::query()
                ->where('user_id', $staff->id)
                ->where('work_date', 'like', $thisMonth.'%')
                ->sum('overtime_minutes'),
        ]);
    }

    public function correct(Request $request, AttendanceRecord $record): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:present,late,absent,on_leave,half_day'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $record->update([
            ...$validated,
            'corrected_by_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Attendance record updated for '.$record->user?->displayName().'.');
    }

    public function locationEdit(): View
    {
        return view('admin.attendance.location', [
            'location' => WorkLocation::query()->where('is_active', true)->firstOrFail(),
            'shiftStart' => SiteSettings::all()['attendance_shift_start'] ?? '08:00',
            'shiftEnd' => SiteSettings::all()['attendance_shift_end'] ?? '20:00',
            'graceMinutes' => SiteSettings::all()['attendance_late_grace_minutes'] ?? 15,
            'holidays' => Holiday::query()->orderBy('date')->get(),
        ]);
    }

    public function holidaysStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'unique:holidays,date'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Holiday::query()->create([
            ...$validated,
            'created_by_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Holiday added — attendance will not be required that day.');
    }

    public function holidaysDestroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return back()->with('status', '"'.$holiday->name.'" removed from the holiday list.');
    }

    public function locationUpdate(Request $request, WorkLocation $location): RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:20', 'max:2000'],
            'shift_start' => ['required', 'date_format:H:i'],
            'shift_end' => ['required', 'date_format:H:i'],
            'late_grace_minutes' => ['required', 'integer', 'min:0', 'max:120'],
        ]);

        // The address itself is intentionally not editable here — it's locked
        // to the registered head office. Only the precise GPS fix, geofence
        // radius, and shift timing are configurable.
        $location->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'radius_meters' => $validated['radius_meters'],
            'updated_by_id' => $request->user()->id,
        ]);

        foreach ([
            'attendance_shift_start' => $validated['shift_start'],
            'attendance_shift_end' => $validated['shift_end'],
            'attendance_late_grace_minutes' => (string) $validated['late_grace_minutes'],
        ] as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'attendance']
            );
        }
        SiteSettings::clearCache();

        return redirect()->route('admin.attendance.location.edit')->with('status', 'Attendance location and shift settings saved.');
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
     * Minutes worked past the configured shift end — documented as overtime.
     * Only counts time on the SAME calendar day as shift end (an auto-closed,
     * forgotten clock-out is never awarded overtime — see AttendanceProcessingService).
     */
    private function calculateOvertimeMinutes(Carbon $clockOutAt): int
    {
        $settings = SiteSettings::all();
        $shiftEnd = (string) ($settings['attendance_shift_end'] ?? '20:00');

        $expectedEnd = $clockOutAt->copy()->setTimeFromTimeString($shiftEnd);

        return $clockOutAt->greaterThan($expectedEnd)
            ? (int) $expectedEnd->diffInMinutes($clockOutAt)
            : 0;
    }

    private function storePhoto(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $result = app(CloudinaryUploadService::class)->storeToBoth(
            $request->file($field),
            'attendance-photos',
            'attendance-photos'
        );

        return $result['cloudinary_public_id'] ?? $result['path'] ?? null;
    }
}
