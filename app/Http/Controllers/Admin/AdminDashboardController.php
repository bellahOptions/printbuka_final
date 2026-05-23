<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTodo;
use App\Models\FinanceEntry;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
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
            'todayTasks' => DailyTodo::query()
                ->where('user_id', $user->id)
                ->whereDate('due_date', today())
                ->orderByRaw("FIELD(status, 'pending', 'working_on_it', 'completed', 'reviewed', 'review_requested', 'approved', 'rejected')")
                ->get(),
            'reviewQueueCount' => in_array($user->role, config('printbuka_admin.todo_review_roles', []), true)
                ? DailyTodo::query()->whereIn('status', ['completed', 'review_requested'])->whereNull('reviewed_at')->count()
                : 0,
            'workingOnStaffCount' => in_array($user->role, config('printbuka_admin.todo_review_roles', []), true)
                ? DailyTodo::query()->where('status', 'working_on_it')->whereDate('due_date', today())->distinct('user_id')->count('user_id')
                : 0,
            'recentOrders' => Order::query()
                ->with('product', 'invoice', 'creatorAdmin', 'briefReceiver')
                ->latest()
                ->limit(6)
                ->get(),
            'mostViewedProducts' => Product::query()
                ->where('is_active', true)
                ->with('category')
                ->orderByDesc('view_count')
                ->orderBy('name')
                ->limit(10)
                ->get(),
        ]);
    }
}
