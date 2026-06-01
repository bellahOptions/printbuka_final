@extends('layouts.admin')

@section('title', 'Download Finance Report | Printbuka')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
            <a href="{{ route('admin.finance.index') }}" class="text-sm font-black text-cyan-300">Finance</a>
            <h1 class="mt-3 text-4xl font-black">Download Finance Report</h1>
            <p class="mt-2 text-sm text-slate-300">Generate and download a PDF report for weekly, monthly, or custom date range with optional filters.</p>
        </div>

        <form action="{{ route('admin.finance.report-download') }}" method="GET" target="_blank" class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 sm:grid-cols-2">

                {{-- Period --}}
                <label class="text-sm font-black sm:col-span-2">
                    Report Period
                    <select name="period" id="periodSelect" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        <option value="weekly">This Week</option>
                        <option value="monthly">This Month</option>
                        <option value="custom">Custom Date Range</option>
                    </select>
                </label>

                {{-- Custom date fields --}}
                <div class="sm:col-span-2" id="customDateFields" style="display: none;">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="text-sm font-black">
                            From Date
                            <input type="date" name="date_from" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        </label>
                        <label class="text-sm font-black">
                            To Date
                            <input type="date" name="date_to" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        </label>
                    </div>
                </div>

                {{-- Type filter --}}
                <label class="text-sm font-black">
                    Transaction Type
                    <select name="type" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        <option value="">All Types</option>
                        <option value="income">Income Only</option>
                        <option value="expense">Expenses Only</option>
                    </select>
                </label>

                {{-- Entry Type filter --}}
                <label class="text-sm font-black">
                    Entry Type
                    <select name="entry_type" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        <option value="">All Entries</option>
                        <option value="credit_from_ceo">Credit from CEO</option>
                    </select>
                </label>

                {{-- Category filter --}}
                <label class="text-sm font-black sm:col-span-2">
                    Category
                    <select name="category" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <button class="mt-6 inline-flex items-center gap-2 rounded-md bg-pink-600 px-6 py-3.5 text-sm font-black text-white transition hover:bg-pink-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Report
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodSelect = document.getElementById('periodSelect');
            const customFields = document.getElementById('customDateFields');

            function toggleCustomFields() {
                customFields.style.display = periodSelect.value === 'custom' ? 'block' : 'none';
            }

            periodSelect.addEventListener('change', toggleCustomFields);
            toggleCustomFields();

            // Auto-set custom date fields to this month on first show
            if (periodSelect.value === 'custom') {
                const now = new Date();
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

                document.querySelector('input[name="date_from"]').value = firstDay.toISOString().split('T')[0];
                document.querySelector('input[name="date_to"]').value = lastDay.toISOString().split('T')[0];
            }
        });
    </script>
@endsection
