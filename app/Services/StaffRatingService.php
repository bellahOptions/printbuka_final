<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Holiday;
use App\Models\StaffActivity;
use App\Models\StaffEvaluation;
use App\Models\StaffRatingSnapshot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Scores every active staff member for a given week/month from four signals
 * — attendance, overtime, the most recent supervisor evaluation, and portal
 * activity — and stores the ranked result as a StaffRatingSnapshot row per
 * staff member per period, which is what drives "Staff of the Week/Month".
 *
 * Weights are a judgment call, not a formula handed down by the business —
 * tune them here if the balance feels off in practice.
 */
class StaffRatingService
{
    private const WEIGHTS = [
        'attendance' => 0.40,
        'supervisor' => 0.30,
        'activity'   => 0.20,
        'overtime'   => 0.10,
    ];

    // Overtime beyond 10 hours in a single period stops adding further score
    // — the point is to reward going the extra mile, not to out-rank
    // everyone else purely by logging the most extra hours.
    private const OVERTIME_CAP_MINUTES = 600;

    // Applied when a staff member has no non-draft evaluation on record yet
    // (e.g. brand-new hire) — deliberately a little below the 3/5 "average"
    // rating, so it neither rewards nor punishes an absent evaluation.
    private const DEFAULT_SUPERVISOR_SCORE = 55.0;

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function currentWeekRange(): array
    {
        $now = now(config('app.business_timezone'));

        return [$now->copy()->startOfWeek(Carbon::MONDAY), $now->copy()->endOfWeek(Carbon::SUNDAY)];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function currentMonthRange(): array
    {
        $now = now(config('app.business_timezone'));

        return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
    }

    public function snapshotCurrentWeek(): Collection
    {
        [$start, $end] = $this->currentWeekRange();

        return $this->computeAndStoreSnapshots('week', $start, $end);
    }

    public function snapshotCurrentMonth(): Collection
    {
        [$start, $end] = $this->currentMonthRange();

        return $this->computeAndStoreSnapshots('month', $start, $end);
    }

    /**
     * Recomputes and upserts a ranked snapshot row for every eligible staff
     * member for the given period. Safe to call repeatedly (e.g. every time
     * the Staff Spotlight modal is opened, and once nightly via cron) — it's
     * an idempotent updateOrCreate keyed on (period_type, period_start, user).
     *
     * @return Collection<int, StaffRatingSnapshot>
     */
    public function computeAndStoreSnapshots(string $periodType, Carbon $start, Carbon $end): Collection
    {
        $staff = $this->eligibleStaff();

        $rows = $staff->map(fn (User $user): array => [
            'user'             => $user,
            'attendance_score' => $this->attendanceScore($user, $start, $end),
            'overtime_score'   => $this->overtimeScore($user, $start, $end),
            'supervisor_score' => $this->supervisorScore($user, $end),
            'raw_activity'     => $this->rawActivityCount($user, $start, $end),
        ]);

        $maxActivity = max(1, (int) $rows->max('raw_activity'));
        $now = now();

        return $rows
            ->map(function (array $row) use ($maxActivity): array {
                $activityScore = round(($row['raw_activity'] / $maxActivity) * 100, 2);

                $total = round(
                    $row['attendance_score'] * self::WEIGHTS['attendance']
                    + $row['supervisor_score'] * self::WEIGHTS['supervisor']
                    + $activityScore * self::WEIGHTS['activity']
                    + $row['overtime_score'] * self::WEIGHTS['overtime'],
                    2
                );

                return [...$row, 'activity_score' => $activityScore, 'total_score' => $total];
            })
            ->sortByDesc('total_score')
            ->values()
            ->map(fn (array $row, int $index): StaffRatingSnapshot => StaffRatingSnapshot::query()->updateOrCreate(
                [
                    'period_type'  => $periodType,
                    'period_start' => $start->toDateString(),
                    'user_id'      => $row['user']->id,
                ],
                [
                    'period_end'       => $end->toDateString(),
                    'attendance_score' => $row['attendance_score'],
                    'overtime_score'   => $row['overtime_score'],
                    'supervisor_score' => $row['supervisor_score'],
                    'activity_score'   => $row['activity_score'],
                    'total_score'      => $row['total_score'],
                    'rank'             => $index + 1,
                    'computed_at'      => $now,
                ]
            ));
    }

    /**
     * @return Collection<int, StaffRatingSnapshot>
     */
    public function leaderboard(string $periodType, Carbon $periodStart, int $limit = 5): Collection
    {
        return StaffRatingSnapshot::query()
            ->where('period_type', $periodType)
            ->where('period_start', $periodStart->toDateString())
            ->with('user')
            ->orderBy('rank')
            ->limit($limit)
            ->get();
    }

    private function eligibleStaff(): Collection
    {
        return User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->get();
    }

    /**
     * Percentage of required business days (Mon–Sat, excluding holidays) the
     * staff member was present/late/half-day for, out of days already due.
     * On-leave days are excused — excluded from both sides of the ratio.
     * Days that haven't happened yet (or today, before it's been processed
     * by AttendanceProcessingService) aren't counted either way.
     */
    private function attendanceScore(User $user, Carbon $start, Carbon $end): float
    {
        $today = now(config('app.business_timezone'))->startOfDay();
        $endDay = $end->copy()->startOfDay()->min($today);

        $records = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn (AttendanceRecord $record): string => Carbon::parse($record->work_date)->toDateString());

        $points = [];
        $cursor = $start->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $dateString = $cursor->toDateString();

            if (! $cursor->isSunday() && ! Holiday::isHoliday($dateString)) {
                $record = $records->get($dateString);

                $points[] = match ($record?->status) {
                    'present'   => 1.0,
                    'late'      => 0.7,
                    'half_day'  => 0.5,
                    'on_leave'  => null,
                    'absent'    => 0.0,
                    default     => $cursor->lt($today) ? 0.0 : null,
                };
            }

            $cursor->addDay();
        }

        $points = array_filter($points, fn (?float $p): bool => $p !== null);

        return $points === [] ? 0.0 : round((array_sum($points) / count($points)) * 100, 2);
    }

    private function overtimeScore(User $user, Carbon $start, Carbon $end): float
    {
        $totalMinutes = (int) AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->sum('overtime_minutes');

        return round(min(1, $totalMinutes / self::OVERTIME_CAP_MINUTES) * 100, 2);
    }

    /**
     * Uses the most recent submitted/acknowledged evaluation as of the
     * period's end (never a later one, so a past period's score can't shift
     * based on an evaluation written after the fact) — falling back to a
     * neutral default when none exists yet.
     */
    private function supervisorScore(User $user, Carbon $periodEnd): float
    {
        $evaluation = StaffEvaluation::query()
            ->where('staff_id', $user->id)
            ->where('status', '!=', 'draft')
            ->where(function ($query) use ($periodEnd): void {
                $query->where('period_year', '<', $periodEnd->year)
                    ->orWhere(function ($query) use ($periodEnd): void {
                        $query->where('period_year', $periodEnd->year)
                            ->where('period_month', '<=', $periodEnd->month);
                    });
            })
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->first();

        if (! $evaluation) {
            return self::DEFAULT_SUPERVISOR_SCORE;
        }

        return round(($evaluation->averageRating() / 5) * 100, 2);
    }

    private function rawActivityCount(User $user, Carbon $start, Carbon $end): int
    {
        return StaffActivity::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->count();
    }
}
