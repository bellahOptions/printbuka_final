<?php

namespace App\Support;

use Carbon\Carbon;

class AttendanceCalculator
{
    /**
     * Minutes worked past the configured shift end — documented as overtime.
     * Shared by the self-service clock-out flow and manager manual entry so
     * both compute overtime the same way.
     */
    public static function overtimeMinutes(Carbon $clockOutAt): int
    {
        $settings = SiteSettings::all();
        $shiftEnd = (string) ($settings['attendance_shift_end'] ?? '20:00');

        $expectedEnd = $clockOutAt->copy()->setTimeFromTimeString($shiftEnd);

        return $clockOutAt->greaterThan($expectedEnd)
            ? (int) $expectedEnd->diffInMinutes($clockOutAt)
            : 0;
    }
}
