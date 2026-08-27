<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\StaffActivity;
use App\Models\StaffEvaluation;
use App\Models\StaffProfile;
use App\Models\StaffRatingSnapshot;
use App\Models\User;
use App\Services\StaffRatingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StaffRatingServiceTest extends TestCase
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

    public function test_snapshot_blends_all_four_signals_and_ranks_the_top_scorer_first(): void
    {
        \Illuminate\Support\Facades\App::make('config')->set('app.business_timezone', 'Africa/Lagos');
        Carbon::setTestNow(Carbon::parse('2026-08-24 12:00:00', 'Africa/Lagos')); // a Monday

        $top = $this->makeStaff('office_assistant');
        $bottom = $this->makeStaff('office_assistant');

        AttendanceRecord::query()->create([
            'user_id' => $top->id,
            'work_date' => '2026-08-24',
            'status' => 'present',
            'overtime_minutes' => 120,
        ]);
        AttendanceRecord::query()->create([
            'user_id' => $bottom->id,
            'work_date' => '2026-08-24',
            'status' => 'absent',
        ]);

        StaffEvaluation::query()->create([
            'staff_id' => $top->id,
            'evaluated_by_id' => $top->id,
            'period_month' => 8,
            'period_year' => 2026,
            'overall_rating' => 5,
            'punctuality_rating' => 5,
            'quality_of_work_rating' => 5,
            'teamwork_rating' => 5,
            'communication_rating' => 5,
            'initiative_rating' => 5,
            'status' => 'submitted',
        ]);

        StaffActivity::query()->create(['user_id' => $top->id, 'action' => 'test.action']);

        $snapshots = app(StaffRatingService::class)->snapshotCurrentWeek();

        [$weekStart] = app(StaffRatingService::class)->currentWeekRange();
        $leaderboard = app(StaffRatingService::class)->leaderboard('week', $weekStart);

        Carbon::setTestNow();

        $this->assertCount(2, $snapshots);

        $topSnapshot = StaffRatingSnapshot::query()->where('user_id', $top->id)->firstOrFail();
        $bottomSnapshot = StaffRatingSnapshot::query()->where('user_id', $bottom->id)->firstOrFail();

        $this->assertSame(100.0, (float) $topSnapshot->attendance_score);
        $this->assertSame(0.0, (float) $bottomSnapshot->attendance_score);

        $this->assertSame(100.0, (float) $topSnapshot->supervisor_score);
        $this->assertSame(55.0, (float) $bottomSnapshot->supervisor_score); // no evaluation on record yet

        $this->assertSame(100.0, (float) $topSnapshot->activity_score); // sole activity this period
        $this->assertSame(0.0, (float) $bottomSnapshot->activity_score);

        $this->assertSame(20.0, (float) $topSnapshot->overtime_score); // 120/600 minutes capped

        // 100*.40 + 100*.30 + 100*.20 + 20*.10 = 92.0
        $this->assertSame(92.0, (float) $topSnapshot->total_score);
        // 0*.40 + 55*.30 + 0*.20 + 0*.10 = 16.5
        $this->assertSame(16.5, (float) $bottomSnapshot->total_score);

        $this->assertSame(1, $topSnapshot->rank);
        $this->assertSame(2, $bottomSnapshot->rank);

        $this->assertSame($top->id, $leaderboard->first()->user_id);
    }

    public function test_staff_of_any_role_can_open_the_staff_spotlight(): void
    {
        $staff = $this->makeStaff('office_assistant');

        $this->actingAs($staff)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.staff-spotlight.index'))
            ->assertOk()
            ->assertSee('Staff of the Week')
            ->assertSee('Staff of the Month');
    }
}
