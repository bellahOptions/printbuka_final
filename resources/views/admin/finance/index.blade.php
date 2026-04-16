@extends('layouts.admin')

@section('title', 'Finance Dashboard | Printbuka')

@section('content')
    @php
        $netIncome = (float) $income - (float) $expenses;
        $profitMargin = $income > 0 ? ($netIncome / $income) * 100 : 0;
        
        // Prepare chart data
        $monthlyData = $entries->groupBy(function($entry) {
            return $entry->entry_date->format('M');
        });
        
        $chartLabels = $monthlyData->keys()->toArray();
        $incomeData = $monthlyData->map(function($month) {
            return $month->where('type', 'income')->sum('amount');
        })->values()->toArray();
        $expenseData = $monthlyData->map(function($month) {
            return $month->where('type', 'expense')->sum('amount');
        })->values()->toArray();
        
        // Category breakdown
        $categoryData = $entries->where('type', 'expense')->groupBy('category')->map(function($group) {
            return $group->sum('amount');
        });
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <!-- Hero Section -->
        <div class="fade-in-up rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-8 text-white shadow-xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <p class="text-sm font-black uppercase tracking-wider text-emerald-300">Financial Overview</p>
                    </div>
                    <h1 class="text-4xl font-black tracking-tight lg:text-5xl">Cash Flow Dashboard</h1>
                    <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-300">Track income, expenses, and financial performance with real-time insights.</p>
                </div>
                <a href="{{ route('admin.finance.create') }}" class="btn-primary group relative inline-flex items-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Entry
                    <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                </a>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="fade-in-up section-delay-1 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="group rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white to-emerald-50/30 p-6 shadow-sm transition-all duration-300 hover:shadow-xl hover:border-emerald-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50">
                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full">+{{ number_format($income > 0 ? (($income - ($income * 0.1)) / $income) * 100 : 0, 1) }}%</span>
                </div>
                <p class="text-sm font-black uppercase tracking-wider text-slate-500">Total Income</p>
                <p class="mt-2 text-3xl font-black text-slate-950">₦{{ number_format((float) $income, 2) }}</p>
                <p class="mt-2 text-xs text-slate-500">All time revenue</p>
            </div>

            <div class="group rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white to-pink-50/30 p-6 shadow-sm transition-all duration-300 hover:shadow-xl hover:border-pink-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-pink-100 to-pink-50">
                        <svg class="w-6 h-6 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-wider text-pink-700 bg-pink-100 px-2 py-1 rounded-full">Outflow</span>
                </div>
                <p class="text-sm font-black uppercase tracking-wider text-slate-500">Total Expenses</p>
                <p class="mt-2 text-3xl font-black text-slate-950">₦{{ number_format((float) $expenses, 2) }}</p>
                <p class="mt-2 text-xs text-slate-500">Operational costs</p>
            </div>

            <div class="group rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white {{ $netIncome >= 0 ? 'to-cyan-50/30' : 'to-red-50/30' }} p-6 shadow-sm transition-all duration-300 hover:shadow-xl {{ $netIncome >= 0 ? 'hover:border-cyan-200' : 'hover:border-red-200' }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl bg-gradient-to-br {{ $netIncome >= 0 ? 'from-cyan-100 to-cyan-50' : 'from-red-100 to-red-50' }}">
                        <svg class="w-6 h-6 {{ $netIncome >= 0 ? 'text-cyan-700' : 'text-red-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-wider {{ $netIncome >= 0 ? 'text-cyan-700 bg-cyan-100' : 'text-red-700 bg-red-100' }} px-2 py-1 rounded-full">
                        {{ $profitMargin >= 0 ? '+' : '' }}{{ number_format($profitMargin, 1) }}%
                    </span>
                </div>
                <p class="text-sm font-black uppercase tracking-wider text-slate-500">Net Income</p>
                <p class="mt-2 text-3xl font-black {{ $netIncome >= 0 ? 'text-cyan-700' : 'text-red-700' }}">₦{{ number_format($netIncome, 2) }}</p>
                <p class="mt-2 text-xs text-slate-500">Profit margin</p>
            </div>

            <div class="group rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white to-purple-50/30 p-6 shadow-sm transition-all duration-300 hover:shadow-xl hover:border-purple-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-purple-100 to-purple-50">
                        <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-wider text-purple-700 bg-purple-100 px-2 py-1 rounded-full">{{ $entries->count() }}</span>
                </div>
                <p class="text-sm font-black uppercase tracking-wider text-slate-500">Total Entries</p>
                <p class="mt-2 text-3xl font-black text-slate-950">{{ $entries->count() }}</p>
                <p class="mt-2 text-xs text-slate-500">Transactions recorded</p>
            </div>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="fade-in-up rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-bold text-emerald-800">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <!-- Charts Section -->
        <div class="fade-in-up section-delay-2 grid gap-6 lg:grid-cols-2">
            <!-- Cash Flow Chart -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Cash Flow Analysis</h2>
                        <p class="text-sm text-slate-500">Monthly income vs expenses</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1.5 text-xs font-semibold">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            Income
                        </span>
                        <span class="flex items-center gap-1.5 text-xs font-semibold">
                            <span class="w-3 h-3 rounded-full bg-pink-500"></span>
                            Expenses
                        </span>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>

            <!-- Expense Categories Chart -->
            <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Expense Breakdown</h2>
                        <p class="text-sm text-slate-500">By category</p>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="fade-in-up section-delay-3 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200/60 bg-white p-5">
                <p class="text-xs font-black uppercase tracking-wider text-slate-500">Average Transaction</p>
                <p class="mt-2 text-2xl font-black text-slate-950">₦{{ number_format($entries->avg('amount') ?? 0, 2) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200/60 bg-white p-5">
                <p class="text-xs font-black uppercase tracking-wider text-slate-500">Largest Income</p>
                <p class="mt-2 text-2xl font-black text-emerald-700">₦{{ number_format($entries->where('type', 'income')->max('amount') ?? 0, 2) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200/60 bg-white p-5">
                <p class="text-xs font-black uppercase tracking-wider text-slate-500">Largest Expense</p>
                <p class="mt-2 text-2xl font-black text-pink-700">₦{{ number_format($entries->where('type', 'expense')->max('amount') ?? 0, 2) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200/60 bg-white p-5">
                <p class="text-xs font-black uppercase tracking-wider text-slate-500">This Month</p>
                <p class="mt-2 text-2xl font-black {{ ($entries->where('entry_date', '>=', now()->startOfMonth())->where('type', 'income')->sum('amount') - $entries->where('entry_date', '>=', now()->startOfMonth())->where('type', 'expense')->sum('amount')) >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    ₦{{ number_format($entries->where('entry_date', '>=', now()->startOfMonth())->where('type', 'income')->sum('amount') - $entries->where('entry_date', '>=', now()->startOfMonth())->where('type', 'expense')->sum('amount'), 2) }}
                </p>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="fade-in-up section-delay-4 rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
                <div>
                    <h2 class="text-lg font-black text-slate-950">Recent Transactions</h2>
                    <p class="text-sm text-slate-500">All financial entries</p>
                </div>
                <div class="flex items-center gap-3">
                    <select class="text-sm rounded-lg border border-slate-300 px-3 py-2 font-semibold" id="filterType">
                        <option value="">All Types</option>
                        <option value="income">Income Only</option>
                        <option value="expense">Expenses Only</option>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80">
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Date</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Type</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Category</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Description</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500 text-right">Amount</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($entries as $entry)
                            <tr class="group transition-all duration-200 hover:bg-slate-50/80">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900">{{ $entry->entry_date->format('M j, Y') }}</p>
                                    <p class="text-xs text-slate-500">{{ $entry->entry_date->format('h:i A') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-black uppercase tracking-wider {{ $entry->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-pink-100 text-pink-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $entry->type === 'income' ? 'bg-emerald-500' : 'bg-pink-500' }}"></span>
                                        {{ ucfirst($entry->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-slate-700">{{ $entry->category }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900">{{ $entry->description }}</p>
                                    @if($entry->reference)
                                        <p class="text-xs text-slate-500">Ref: {{ $entry->reference }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class="font-black {{ $entry->type === 'income' ? 'text-emerald-700' : 'text-pink-700' }}">
                                        {{ $entry->type === 'income' ? '+' : '-' }} ₦{{ number_format((float) $entry->amount, 2) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.finance.edit', $entry) }}" class="rounded-lg p-2 text-pink-700 transition-all duration-200 hover:bg-pink-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.finance.destroy', $entry) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this entry?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button class="rounded-lg p-2 text-slate-500 transition-all duration-200 hover:bg-red-50 hover:text-red-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="rounded-full bg-slate-100 p-4">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-500">No finance entries yet.</p>
                                        <a href="{{ route('admin.finance.create') }}" class="text-sm font-bold text-pink-700 hover:text-pink-800">Add your first entry →</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($entries->hasPages())
            <div class="fade-in-up">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        
        .section-delay-1 { animation-delay: 0.05s; }
        .section-delay-2 { animation-delay: 0.1s; }
        .section-delay-3 { animation-delay: 0.15s; }
        .section-delay-4 { animation-delay: 0.2s; }
        
        .btn-primary {
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:active {
            transform: scale(0.98);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cash Flow Chart
            const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
            new Chart(cashFlowCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Income',
                        data: {!! json_encode($incomeData) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }, {
                        label: 'Expenses',
                        data: {!! json_encode($expenseData) !!},
                        borderColor: '#ec4899',
                        backgroundColor: 'rgba(236, 72, 153, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ec4899',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '₦' + context.parsed.y.toLocaleString();
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e2e8f0'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₦' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryLabels = {!! json_encode($categoryData->keys()->toArray()) !!};
            const categoryValues = {!! json_encode($categoryData->values()->toArray()) !!};
            
            const colors = [
                '#ec4899', '#06b6d4', '#10b981', '#f59e0b', '#6366f1',
                '#8b5cf6', '#ef4444', '#14b8a6', '#f97316', '#3b82f6'
            ];
            
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryValues,
                        backgroundColor: colors.slice(0, categoryLabels.length),
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 15,
                                font: {
                                    weight: '600',
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ₦' + value.toLocaleString() + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });

            // Filter functionality
            const filterSelect = document.getElementById('filterType');
            filterSelect.addEventListener('change', function() {
                const filterValue = this.value;
                const rows = document.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    if (row.cells.length > 1) {
                        const typeCell = row.cells[1];
                        const typeText = typeCell.textContent.trim().toLowerCase();
                        
                        if (!filterValue || typeText.includes(filterValue)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        });
    </script>
@endsection