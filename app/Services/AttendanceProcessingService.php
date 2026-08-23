<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Holiday;
use App\Models\User;
use App\Support\SiteSettings;
use Carbon\Carbon;

class AttendanceProcessingService
{
    /**
     * Mark no-shows as absent past the shift cutoff, and auto-close any
     * forgotten clock-outs left open from previous days.
     *
     * @return array{absent: int, closed: int}
     */
    public function processDaily(): array
    {
        $timezone = config('app.business_timezone');
        $now = now($timezone);
        $today = $now->toDateString();

        return [
            'absent' => $this->markAbsentees($today, $now),
            'closed' => $this->closeOrphanedSessions($today),
        ];
    }

    private function markAbsentees(string $today, Carbon $now): int
    {
        // Attendance is mandatory Monday–Saturday only, and never on a
        // registered holiday — no absence marking on either.
        if ($now->isSunday() || Holiday::isHoliday($today)) {
            return 0;
        }

        $settings = SiteSettings::all();
        $shiftStart = (string) ($settings['attendance_shift_start'] ?? '08:00');
        $graceMinutes = (int) ($settings['attendance_late_grace_minutes'] ?? 15);

        // Only run once the shift + a generous cutoff window has actually passed,
        // so this is safe to schedule hourly without prematurely marking people absent.
        $cutoff = $now->copy()->setTimeFromTimeString($shiftStart)->addMinutes($graceMinutes)->addHours(2);
        if ($now->lessThan($cutoff)) {
            return 0;
        }

        $alreadyRecorded = AttendanceRecord::query()
            ->where('work_date', $today)
            ->pluck('user_id');

        $missing = User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->whereNotIn('id', $alreadyRecorded)
            ->get();

        foreach ($missing as $staff) {
            AttendanceRecord::query()->create([
                'user_id' => $staff->id,
                'work_date' => $today,
                'status' => 'absent',
                'flagged_reason' => 'No clock-in recorded by '.$cutoff->format('h:i A').'.',
            ]);
        }

        return $missing->count();
    }

    private function closeOrphanedSessions(string $today): int
    {
        $orphaned = AttendanceRecord::query()
            ->whereNotNull('clock_in_at')
            ->whereNull('clock_out_at')
            ->where('work_date', '<', $today)
            ->get();

        foreach ($orphaned as $record) {
            $record->update([
                'clock_out_at' => $record->clock_in_at->copy()->endOfDay(),
                'flagged_reason' => trim(($record->flagged_reason ? $record->flagged_reason.' ' : '').'Auto-closed — clock-out was never recorded.'),
            ]);
        }

        return $orphaned->count();
    }
}
