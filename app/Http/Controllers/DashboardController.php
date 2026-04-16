<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user?->hasAdminAccess()) {
            return redirect()->route('admin.dashboard');
        }

        $userId = Auth::id();

        $orders = Order::where('user_id', $userId)
            ->where('status', 'ongoing')
            ->count();

        $recentOrders = Order::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'orders' => $orders,
            'recentOrders' => $recentOrders,
        ]);
    }
}
