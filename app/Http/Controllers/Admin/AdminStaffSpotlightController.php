<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StaffRatingService;
use Illuminate\View\View;

class AdminStaffSpotlightController extends Controller
{
    /**
     * Renders the "Staff of the Week / Month" leaderboard as a bare HTML
     * fragment — loaded into the Staff Spotlight modal (any staff member,
     * see routes/admin.php) via a fetch() call, not a full page visit.
     */
    public function index(StaffRatingService $ratings): View
    {
        [$weekStart] = $ratings->currentWeekRange();
        [$monthStart] = $ratings->currentMonthRange();

        $ratings->snapshotCurrentWeek();
        $ratings->snapshotCurrentMonth();

        return view('admin.staff-spotlight', [
            'weekLeaders' => $ratings->leaderboard('week', $weekStart),
            'monthLeaders' => $ratings->leaderboard('month', $monthStart),
        ]);
    }
}
