<?php

namespace Tests\Feature;

use App\Livewire\Admin\AttendanceClock;
use App\Models\AttendanceRecord;
use App\Models\StaffProfile;
use App\Models\User;
use App\Models\WorkLocation;
use App\Services\AttendanceProcessingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(string $role): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        StaffProfile::query()->create([
            'user_id' => $user->id,
            'kyc_status' => 'approved',
        ]);

        return $user;
    }

    public function test_any_staff_member_can_view_their_attendance_page(): void
    {
        $staff = $this->makeStaff('office_assistant');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.attendance.index'))
            ->assertOk();
    }

    public function test_staff_can_clock_in_and_out(): void
    {
        $staff = $this->makeStaff('office_assistant');
        $location = WorkLocation::query()->first();

        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn', (float) $location->latitude, (float) $location->longitude, 10)
            ->assertHasNoErrors();

        $record = AttendanceRecord::query()->where('user_id', $staff->id)->firstOrFail();
        $this->assertNotNull($record->clock_in_at);
        $this->assertTrue($record->clock_in_within_geofence);
        $this->assertNull($record->clock_out_at);

        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockOut', (float) $location->latitude, (float) $location->longitude, 10)
            ->assertHasNoErrors();

        $this->assertNotNull($record->fresh()->clock_out_at);
    }

    public function test_cannot_clock_in_twice_in_the_same_day(): void
    {
        $staff = $this->makeStaff('office_assistant');
        $location = WorkLocation::query()->first();

        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn', (float) $location->latitude, (float) $location->longitude)
            ->assertHasNoErrors();

        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn', (float) $location->latitude, (float) $location->longitude)
            ->assertHasErrors('clockIn');
    }

    public function test_cannot_clock_out_without_clocking_in(): void
    {
        $staff = $this->makeStaff('office_assistant');
        $location = WorkLocation::query()->first();

        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockOut', (float) $location->latitude, (float) $location->longitude)
            ->assertHasErrors('clockOut');
    }

    public function test_clocking_in_without_a_location_fix_is_blocked(): void
    {
        $staff = $this->makeStaff('office_assistant');

        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn')
            ->assertHasErrors('clockIn');

        $this->assertDatabaseMissing('attendance_records', ['user_id' => $staff->id]);
    }

    public function test_clocking_in_far_from_the_office_is_blocked(): void
    {
        $staff = $this->makeStaff('office_assistant');

        // Roughly 5km away from the seeded head-office coordinates.
        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn', 6.58, 3.42)
            ->assertHasErrors('clockIn');

        $this->assertDatabaseMissing('attendance_records', ['user_id' => $staff->id]);
    }

    public function test_clocking_out_far_from_the_office_is_flagged_but_not_blocked(): void
    {
        $staff = $this->makeStaff('office_assistant');
        $location = WorkLocation::query()->first();

        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn', (float) $location->latitude, (float) $location->longitude)
            ->assertHasNoErrors();

        // Roughly 5km away from the seeded head-office coordinates.
        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockOut', 6.58, 3.42)
            ->assertHasNoErrors();

        $record = AttendanceRecord::query()->where('user_id', $staff->id)->firstOrFail();
        $this->assertNotNull($record->clock_out_at);
        $this->assertFalse($record->clock_out_within_geofence);
    }

    public function test_hr_can_view_team_attendance_and_correct_a_record(): void
    {
        $hr = $this->makeStaff('hr');
        $staff = $this->makeStaff('office_assistant');
        $location = WorkLocation::query()->first();

        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn', (float) $location->latitude, (float) $location->longitude)
            ->assertHasNoErrors();

        $record = AttendanceRecord::query()->where('user_id', $staff->id)->firstOrFail();

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.attendance.team'))
            ->assertOk()
            ->assertSee($staff->displayName());

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->patch(route('admin.attendance.correct', $record), [
                'status' => 'half_day',
                'notes' => 'Left early for a hospital visit.',
            ])
            ->assertRedirect();

        $this->assertSame('half_day', $record->fresh()->status);
        $this->assertSame($hr->id, $record->fresh()->corrected_by_id);
    }

    public function test_staff_without_attendance_manage_permission_cannot_view_team_attendance(): void
    {
        $staff = $this->makeStaff('office_assistant');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.attendance.team'))
            ->assertForbidden();
    }

    public function test_operations_manager_can_update_location_and_shift_settings(): void
    {
        $manager = $this->makeStaff('operations_manager');
        $location = WorkLocation::query()->first();

        $this->actingAs($manager)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.attendance.location.edit'))
            ->assertOk()
            ->assertSee($location->address);

        $this->actingAs($manager)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.attendance.location.update', $location), [
                'latitude' => 6.539123,
                'longitude' => 3.385456,
                'radius_meters' => 150,
                'shift_start' => '08:30',
                'shift_end' => '17:30',
                'late_grace_minutes' => 10,
            ])
            ->assertRedirect();

        $location->refresh();
        $this->assertSame('150', (string) $location->radius_meters);
        $this->assertSame('08:30', \App\Support\SiteSettings::all()['attendance_shift_start']);
    }

    public function test_process_daily_marks_absentees_and_closes_orphaned_sessions(): void
    {
        $staff = $this->makeStaff('office_assistant');
        $absentee = $this->makeStaff('hr');

        // Yesterday: staff clocked in but never out.
        AttendanceRecord::query()->create([
            'user_id' => $staff->id,
            'work_date' => now()->subDay()->toDateString(),
            'clock_in_at' => now()->subDay()->setTime(8, 0),
        ]);

        // Pin "now" to a known Monday, well past the shift cutoff — deterministic
        // regardless of which real-world weekday the suite happens to run on.
        \Illuminate\Support\Facades\App::make('config')->set('app.business_timezone', 'Africa/Lagos');
        \App\Models\SiteSetting::query()->create(['key' => 'attendance_shift_start', 'value' => '00:00', 'group' => 'attendance']);
        \App\Support\SiteSettings::clearCache();
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-24 12:00:00', 'Africa/Lagos')); // a Monday

        $result = app(AttendanceProcessingService::class)->processDaily();

        \Illuminate\Support\Carbon::setTestNow();

        $this->assertGreaterThanOrEqual(1, $result['absent']);
        $this->assertSame(1, $result['closed']);

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $absentee->id,
            'status' => 'absent',
        ]);

        $yesterdayRecord = AttendanceRecord::query()
            ->where('user_id', $staff->id)
            ->where('work_date', now()->subDay()->toDateString())
            ->firstOrFail();
        $this->assertNotNull($yesterdayRecord->clock_out_at);
    }

    public function test_no_one_is_marked_absent_on_a_sunday(): void
    {
        $this->makeStaff('office_assistant');

        \App\Models\SiteSetting::query()->create(['key' => 'attendance_shift_start', 'value' => '00:00', 'group' => 'attendance']);
        \App\Support\SiteSettings::clearCache();
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-23 12:00:00', 'Africa/Lagos')); // a Sunday

        $result = app(AttendanceProcessingService::class)->processDaily();

        \Illuminate\Support\Carbon::setTestNow();

        $this->assertSame(0, $result['absent']);
        $this->assertDatabaseMissing('attendance_records', ['status' => 'absent']);
    }

    public function test_no_one_is_marked_absent_on_a_registered_holiday(): void
    {
        $this->makeStaff('office_assistant');

        \App\Models\Holiday::query()->create(['date' => '2026-08-24', 'name' => 'Test Holiday']);
        \App\Models\SiteSetting::query()->create(['key' => 'attendance_shift_start', 'value' => '00:00', 'group' => 'attendance']);
        \App\Support\SiteSettings::clearCache();
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-24 12:00:00', 'Africa/Lagos')); // Monday, but a holiday

        $result = app(AttendanceProcessingService::class)->processDaily();

        \Illuminate\Support\Carbon::setTestNow();

        $this->assertSame(0, $result['absent']);
        $this->assertDatabaseMissing('attendance_records', ['status' => 'absent']);
    }

    public function test_clocking_out_after_shift_end_records_overtime(): void
    {
        $staff = $this->makeStaff('office_assistant');
        $location = WorkLocation::query()->first();

        \App\Models\SiteSetting::query()->create(['key' => 'attendance_shift_end', 'value' => '20:00', 'group' => 'attendance']);
        \App\Support\SiteSettings::clearCache();

        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-24 08:00:00', 'Africa/Lagos'));
        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn', (float) $location->latitude, (float) $location->longitude)
            ->assertHasNoErrors();

        // Clock out 45 minutes past the 8pm shift end.
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-24 20:45:00', 'Africa/Lagos'));
        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockOut', (float) $location->latitude, (float) $location->longitude)
            ->assertHasNoErrors();

        \Illuminate\Support\Carbon::setTestNow();

        $record = AttendanceRecord::query()->where('user_id', $staff->id)->firstOrFail();
        $this->assertSame(45, $record->overtime_minutes);
        $this->assertTrue($record->hasOvertime());
    }

    public function test_clocking_out_before_shift_end_records_no_overtime(): void
    {
        $staff = $this->makeStaff('office_assistant');
        $location = WorkLocation::query()->first();

        \App\Models\SiteSetting::query()->create(['key' => 'attendance_shift_end', 'value' => '20:00', 'group' => 'attendance']);
        \App\Support\SiteSettings::clearCache();

        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-24 08:00:00', 'Africa/Lagos'));
        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockIn', (float) $location->latitude, (float) $location->longitude)
            ->assertHasNoErrors();

        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-24 18:00:00', 'Africa/Lagos'));
        Livewire::actingAs($staff)
            ->test(AttendanceClock::class)
            ->call('clockOut', (float) $location->latitude, (float) $location->longitude)
            ->assertHasNoErrors();

        \Illuminate\Support\Carbon::setTestNow();

        $record = AttendanceRecord::query()->where('user_id', $staff->id)->firstOrFail();
        $this->assertSame(0, $record->overtime_minutes);
        $this->assertFalse($record->hasOvertime());
    }

    public function test_hr_can_manage_holidays(): void
    {
        $hr = $this->makeStaff('hr');

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.attendance.holidays.store'), [
                'date' => '2026-12-25',
                'name' => 'Christmas Day',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('holidays', ['date' => '2026-12-25', 'name' => 'Christmas Day']);

        $holiday = \App\Models\Holiday::query()->firstOrFail();

        $this->actingAs($hr)
            ->withSession(['staff_2fa_verified' => true])
            ->delete(route('admin.attendance.holidays.destroy', $holiday))
            ->assertRedirect();

        $this->assertDatabaseMissing('holidays', ['id' => $holiday->id]);
    }
}
