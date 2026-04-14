<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceEntry;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $menus = config('printbuka_admin.staff_dashboard_menus.'.$user->role, ['Job Information']);
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthOrders = Order::query()->whereBetween('created_at', [$monthStart, $monthEnd]);
        $monthInvoices = Invoice::query()->whereBetween('created_at', [$monthStart, $monthEnd]);
        $monthFinance = FinanceEntry::query()->whereBetween('entry_date', [$monthStart, $monthEnd]);
        $totalRevenue = (float) (clone $monthOrders)->sum('amount_paid');
        $financeIncome = (float) (clone $monthFinance)->where('type', 'income')->sum('amount');
        $totalRevenue += $financeIncome;
        $totalExpenses = (float) (clone $monthFinance)->where('type', 'expense')->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;
        $totalInvoiced = (float) (clone $monthInvoices)->sum('total_amount');
        $outstandingBalances = (float) (clone $monthOrders)
            ->get(['total_price', 'amount_paid'])
            ->sum(fn (Order $order): float => max((float) $order->total_price - (float) $order->amount_paid, 0));
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        $jobStatusCounts = collect(config('printbuka_admin.job_statuses'))->mapWithKeys(fn (string $status): array => [
            $status => Order::query()->where('status', $status)->count(),
        ]);
        $weeklyProfitSnapshot = collect(range(1, 8))->map(function (int $week) use ($monthStart): array {
            $start = $monthStart->copy()->addWeeks($week - 1);
            $end = $start->copy()->endOfWeek();
            $revenue = (float) Order::query()->whereBetween('created_at', [$start, $end])->sum('amount_paid');
            $expenses = (float) FinanceEntry::query()->where('type', 'expense')->whereBetween('entry_date', [$start, $end])->sum('amount');
            $revenue += (float) FinanceEntry::query()->where('type', 'income')->whereBetween('entry_date', [$start, $end])->sum('amount');
            $profit = $revenue - $expenses;

            return [
                'week' => $week,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
                'status' => $profit > 0 ? 'Profit' : 'Loss',
            ];
        });

        return view('admin.dashboard', [
            'orderCount' => Order::count(),
            'activeJobs' => Order::query()->whereNotIn('status', ['Delivered', 'Cancelled'])->count(),
            'deliveredJobs' => Order::query()->where('status', 'Delivered')->count(),
            'staffCount' => User::query()->where('role', '!=', 'customer')->count(),
            'pendingStaffCount' => User::query()->where('role', 'staff_pending')->orWhere(fn ($query) => $query->where('is_active', false)->whereNotNull('requested_role'))->count(),
            'adminRoleLabel' => config('printbuka_admin.role_labels.'.$user->role, $user->role),
            'dashboardMenus' => $menus,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'profitMargin' => $profitMargin,
            'outstandingBalances' => $outstandingBalances,
            'totalInvoiced' => $totalInvoiced,
            'jobStatusCounts' => $jobStatusCounts,
            'weeklyProfitSnapshot' => $weeklyProfitSnapshot,
            'workflowPhases' => config('printbuka_admin.workflow_phases'),
            'recentOrders' => Order::query()
                ->with('product', 'invoice')
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }
}
