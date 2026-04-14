<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(): View
{
    $userId = Auth::id();

    // Count of ongoing orders for this user
    $orders = Order::where('user_id', $userId)
        ->where('status', 'ongoing')
        ->count();

    // Recent orders for this user
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
