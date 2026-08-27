<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\AttendanceRecord;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\WorkLocation;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.attendance.index', [
            'location' => WorkLocation::query()->where('is_active', true)->first(),
        ]);
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
}
