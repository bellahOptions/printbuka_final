<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'orderCount' => Order::count(),
            'activeJobs' => Order::query()->whereNotIn('status', ['Delivered', 'Cancelled'])->count(),
            'deliveredJobs' => Order::query()->where('status', 'Delivered')->count(),
            'staffCount' => User::query()->where('role', '!=', 'customer')->count(),
            'workflowPhases' => config('printbuka_admin.workflow_phases'),
            'recentOrders' => Order::query()
                ->with('product', 'invoice')
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }
}
