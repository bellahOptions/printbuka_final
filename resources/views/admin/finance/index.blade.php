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
            return $month->where('type', 'income')->where('status', '!=', 'refunded')->sum('amount');
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
        {{-- Header --}}
        <div class="animate-fade-in-up pb-page-header">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="pb-status-dot pb-status-online"><span></span><span></span></span>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Financial Overview</p>
                </div>
                <h1 class="pb-page-title">Cash Flow Dashboard</h1>
                <p class="pb-page-subtitle max-w-2xl">Track income, expenses, and financial performance with real-time insights.</p>
            </div>
            @if (auth()->user()?->canAdmin('finance.view'))
                <a href="{{ route('admin.finance.create') }}" class="pb-btn pb-btn-md pb-btn-primary self-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Entry
                </a>
            @endif
        </div>

        <!-- KPI Cards -->
        <div class="animate-fade-in-up delay-100 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <article class="pb-kpi-card">
                <div class="pb-kpi-accent-bar bg-emerald-500"></div>
                <div class="flex items-start justify-between gap-3 mt-1">
                    <div>
                        <p class="pb-stat-label">Total Income</p>
                        <p class="pb-stat-value text-emerald-700 truncate" title="₦{{ number_format((float) $income, 2) }}">{{ \App\Support\CompactNumber::currency((float) $income) }}</p>
                        <p class="mt-2 text-xs text-slate-500">All time revenue</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>
            </article>

            <article class="pb-kpi-card">
                <div class="pb-kpi-accent-bar bg-brand-500"></div>
                <div class="flex items-start justify-between gap-3 mt-1">
                    <div>
                        <p class="pb-stat-label">Total Expenses</p>
                        <p class="pb-stat-value text-brand-700 truncate" title="₦{{ number_format((float) $expenses, 2) }}">{{ \App\Support\CompactNumber::currency((float) $expenses) }}</p>
                        <p class="mt-2 text-xs text-slate-500">Operational costs</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-100">
                        <svg class="w-5 h-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </span>
                </div>
            </article>

            <article class="pb-kpi-card">
                <div class="pb-kpi-accent-bar {{ $netIncome >= 0 ? 'bg-cyan-500' : 'bg-red-500' }}"></div>
                <div class="flex items-start justify-between gap-3 mt-1">
                    <div>
                        <p class="pb-stat-label">Net Income</p>
                        <p class="pb-stat-value {{ $netIncome >= 0 ? 'text-cyan-700' : 'text-red-700' }} truncate" title="₦{{ number_format($netIncome, 2) }}">{{ \App\Support\CompactNumber::currency($netIncome) }}</p>
                        <p class="mt-2 text-xs text-slate-500">{{ $profitMargin >= 0 ? '+' : '' }}{{ number_format($profitMargin, 1) }}% profit margin</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $netIncome >= 0 ? 'bg-cyan-100' : 'bg-red-100' }}">
                        <svg class="w-5 h-5 {{ $netIncome >= 0 ? 'text-cyan-700' : 'text-red-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                </div>
            </article>

            <article class="pb-kpi-card">
                <div class="pb-kpi-accent-bar bg-violet-500"></div>
                <div class="flex items-start justify-between gap-3 mt-1">
                    <div>
                        <p class="pb-stat-label">Total Entries</p>
                        <p class="pb-stat-value">{{ $entries->count() }}</p>
                        <p class="mt-2 text-xs text-slate-500">Transactions recorded</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-violet-100">
                        <svg class="w-5 h-5 text-violet-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </span>
                </div>
            </article>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="animate-fade-in-up pb-alert pb-alert-success">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <!-- Charts Section -->
        <div class="animate-fade-in-up delay-200 grid gap-6 lg:grid-cols-2">
            <!-- Cash Flow Chart -->
            <div class="pb-card pb-card-content">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="pb-section-title">Cash Flow Analysis</h2>
                        <p class="pb-section-subtitle">Monthly income vs expenses</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            Income
                        </span>
                        <span class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-brand-500"></span>
                            Expenses
                        </span>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>

            <!-- Expense Categories Chart -->
            <div class="pb-card pb-card-content">
                <div class="mb-6">
                    <h2 class="pb-section-title">Expense Breakdown</h2>
                    <p class="pb-section-subtitle">By category</p>
                </div>
                <div class="relative h-80">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="animate-fade-in-up delay-300 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="pb-stat-card">
                <p class="pb-stat-label">Average Transaction</p>
                <p class="mt-2 text-2xl font-bold text-slate-900">₦{{ number_format($entries->avg('amount') ?? 0, 2) }}</p>
            </div>
            <div class="pb-stat-card">
                <p class="pb-stat-label">Largest Income</p>
                <p class="mt-2 text-2xl font-bold text-emerald-700">₦{{ number_format($entries->where('type', 'income')->where('status', '!=', 'refunded')->max('amount') ?? 0, 2) }}</p>
            </div>
            <div class="pb-stat-card">
                <p class="pb-stat-label">Largest Expense</p>
                <p class="mt-2 text-2xl font-bold text-brand-700">₦{{ number_format($entries->where('type', 'expense')->max('amount') ?? 0, 2) }}</p>
            </div>
            <div class="pb-stat-card">
                @php
                    $monthIncome = $entries->where('entry_date', '>=', now()->startOfMonth())->where('type', 'income')->where('status', '!=', 'refunded')->sum('amount');
                    $monthExpense = $entries->where('entry_date', '>=', now()->startOfMonth())->where('type', 'expense')->sum('amount');
                @endphp
                <p class="pb-stat-label">This Month</p>
                <p class="mt-2 text-2xl font-bold {{ ($monthIncome - $monthExpense) >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    ₦{{ number_format($monthIncome - $monthExpense, 2) }}
                </p>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="animate-fade-in-up delay-400 pb-card overflow-hidden">
            <div class="border-b border-slate-100 p-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="pb-card-title">Recent Transactions</h2>
                    <p class="pb-card-description">All financial entries</p>
                </div>
                <div class="flex items-center gap-3">
                    <select class="pb-select" id="filterType" name="type">
                        <option value="">All Types</option>
                        <option value="income">Income Only</option>
                        <option value="expense">Expenses Only</option>
                    </select>
                    <a href="{{ route('admin.finance.report-form') }}" class="pb-btn pb-btn-md pb-btn-outline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Report
                    </a>
                </div>
            </div>

            <livewire:admin.finance-entries-table :filters="$filters" />
        </div>
    </div>

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
