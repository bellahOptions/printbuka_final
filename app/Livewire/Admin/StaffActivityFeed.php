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
            'roleCounts' => User::query()
                ->where('role', '!=', 'customer')
                ->select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->orderByDesc('total')
                ->get(),
            'activitiesToday' => StaffActivity::query()->whereDate('created_at', today())->count(),
            'lastUpdated' => now()->format('H:i:s'),
        ]);
    }
}
