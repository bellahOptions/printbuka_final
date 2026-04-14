<?php

namespace App\Livewire\Admin;

use App\Models\StaffActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StaffActivityFeed extends Component
{
    public function render()
    {
        return view('livewire.admin.staff-activity-feed', [
            'activities' => StaffActivity::query()
                ->with('user')
                ->latest()
                ->limit(8)
                ->get(),
            'topPerformers' => StaffActivity::query()
                ->with('user')
                ->whereNotNull('user_id')
                ->whereDate('created_at', today())
                ->select('user_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_action_at'))
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
            'roleCounts' => User::query()
                ->where('role', '!=', 'customer')
                ->select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->orderByDesc('total')
                ->get(),
            'staffTotal' => User::query()->where('role', '!=', 'customer')->count(),
            'activitiesToday' => StaffActivity::query()->whereDate('created_at', today())->count(),
            'lastUpdated' => now()->format('H:i:s'),
        ]);
    }
}
