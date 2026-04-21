<?php

namespace App\Livewire\Admin;

use App\Models\FinanceEntry;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\StaffActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RealtimeStats extends Component
{
    public function render()
    {
        $user = auth()->user();
        $canViewFinance = $user->canAdmin('*') || $user->canAdmin('finance.view');
        $visitorStats = $this->visitorStats();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $financeCards = [];

        if ($canViewFinance) {
            $monthOrders = Order::query()->whereBetween('created_at', [$monthStart, $monthEnd]);
            $monthInvoices = Invoice::query()->whereBetween('created_at', [$monthStart, $monthEnd]);
            $monthFinance = FinanceEntry::query()->whereBetween('entry_date', [$monthStart, $monthEnd]);
            $totalRevenue = (float) (clone $monthOrders)->sum('amount_paid');
            $financeIncome = (float) (clone $monthFinance)->where('type', 'income')->sum('amount');
            $totalRevenue += $financeIncome;
            $totalExpenses = (float) (clone $monthFinance)->where('type', 'expense')->sum('amount');
            $netProfit = $totalRevenue - $totalExpenses;
            $outstandingBalances = (float) (clone $monthOrders)
                ->get(['total_price', 'amount_paid'])
                ->sum(fn (Order $order): float => max((float) $order->total_price - (float) $order->amount_paid, 0));

            $financeCards = [
                ['label' => 'Revenue This Month', 'value' => $totalRevenue],
                ['label' => 'Expenses This Month', 'value' => $totalExpenses],
                ['label' => 'Net Profit', 'value' => $netProfit],
                ['label' => 'Outstanding Balances', 'value' => $outstandingBalances],
                ['label' => 'Total Invoiced', 'value' => (float) (clone $monthInvoices)->sum('total_amount')],
                ['label' => 'Profit Margin', 'value' => $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0, 'suffix' => '%'],
            ];
        }

        return view('livewire.admin.realtime-stats', [
            'canViewFinance' => $canViewFinance,
            'cards' => [
                ['label' => 'Orders', 'value' => Order::count(), 'tone' => 'text-pink-700'],
                ['label' => 'Active Jobs', 'value' => Order::query()->whereNotIn('status', ['Delivered', 'Cancelled'])->count(), 'tone' => 'text-cyan-700'],
                ['label' => 'Delivered', 'value' => Order::query()->where('status', 'Delivered')->count(), 'tone' => 'text-emerald-700'],
                ['label' => 'Staff Online', 'value' => $this->onlineStaffCount(), 'tone' => 'text-amber-700'],
                ['label' => 'Visitors Online', 'value' => $visitorStats['online'], 'tone' => 'text-indigo-700'],
                ['label' => 'Visitors Today', 'value' => $visitorStats['today'], 'tone' => 'text-violet-700'],
            ],
            'financeCards' => $financeCards,
            'jobStatusCounts' => $this->jobStatusCounts(),
            'activityCountToday' => StaffActivity::query()->whereDate('created_at', today())->count(),
            'lastUpdated' => now()->format('H:i:s'),
        ]);
    }

    private function onlineStaffCount(): int
    {
        return (int) DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('sessions.last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->where('users.role', '!=', 'customer')
            ->count();
    }

    private function jobStatusCounts(): Collection
    {
        return collect(config('printbuka_admin.job_statuses'))->mapWithKeys(fn (string $status): array => [
            $status => Order::query()->where('status', $status)->count(),
        ]);
    }

    /**
     * @return array{online:int,today:int}
     */
    private function visitorStats(): array
    {
        $onlineWindow = now()->subMinutes(5)->timestamp;
        $todayStart = now()->startOfDay()->timestamp;

        $visitorSessions = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->where(function ($query): void {
                $query
                    ->whereNull('sessions.user_id')
                    ->orWhere('users.role', 'customer');
            });

        return [
            'online' => (int) (clone $visitorSessions)->where('sessions.last_activity', '>=', $onlineWindow)->count(),
            'today' => (int) (clone $visitorSessions)->where('sessions.last_activity', '>=', $todayStart)->count(),
        ];
    }
}
