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

        // Calculate order statistics
        $orders = Order::where('user_id', $userId)
            ->where('status', 'ongoing')
            ->count();

        // Total spent across all non-cancelled orders
        $totalSpent = Order::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price') ?? 0;

        // Pending invoices (orders with pending payment)
        $pendingInvoices = Order::where('user_id', $userId)
            ->where('payment_status', 'pending')
            ->count() ?? 0;

        // Completed orders
        $completedOrders = Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->count() ?? 0;

        // Active quotes (if Quote model exists)
        $activeQuotes = 0;
        if (class_exists('App\Models\Quote')) {
            $activeQuotes = \App\Models\Quote::where('user_id', $userId)
                ->where('status', 'pending')
                ->count() ?? 0;
        }

        $recentOrders = Order::where('user_id', $userId)
            ->with('product') // Eager load product relationship
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'orders' => $orders,
            'totalSpent' => $totalSpent,
            'pendingInvoices' => $pendingInvoices,
            'completedOrders' => $completedOrders,
            'activeQuotes' => $activeQuotes,
            'recentOrders' => $recentOrders,
        ]);
    }
}