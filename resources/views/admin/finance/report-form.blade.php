@extends('layouts.admin')

@section('title', 'Finance Report | Printbuka Admin')

@section('content')
<div class="mx-auto max-w-[1440px] space-y-6">

    {{-- ════ HERO ════ --}}
    <section class="animate-fade-in-up pb-card overflow-hidden">
        <div class="h-1 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-400"></div>
        <div class="flex flex-col gap-5 p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="pb-badge pb-badge-success">Finance Reports</span>
                    <span class="text-xs text-slate-500">Generate and export PDF financial statements</span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Download Finance Report</h1>
                <p class="max-w-lg text-sm text-slate-500">
                    Configure your report parameters below — choose a period, apply filters, and download a
                    professionally formatted PDF ledger.
                </p>
            </div>
            <a href="{{ route('admin.finance.index') }}"
               class="pb-btn pb-btn-md pb-btn-outline self-start text-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Finance
            </a>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ════ FORM ════ --}}
        <form action="{{ route('admin.finance.report-download') }}"
              method="GET"
              target="_blank"
              class="animate-fade-in-up delay-100 lg:col-span-2">
            @csrf

            <div class="pb-card overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-4 w-1 rounded-full bg-emerald-500"></div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Step 1 of 2 — Period</p>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Select report period</h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Period selector --}}
                    <div class="pb-field">
                        <label class="pb-label" for="periodSelect">Report Period <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3 mt-2" id="periodPicker">
                            @foreach(['weekly' => ['This Week', 'M j – M j', 'week'], 'monthly' => ['This Month', 'Full calendar month', 'month'], 'custom' => ['Custom Range', 'Pick your own dates', 'custom']] as $val => [$label, $desc, $icon])
                            <label class="period-option relative flex cursor-pointer flex-col gap-1 rounded-xl border-2 border-slate-200 bg-white p-4 transition-all hover:border-emerald-300 hover:bg-emerald-50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="period" value="{{ $val }}"
                                       {{ $val === 'weekly' ? 'checked' : '' }}
                                       class="peer sr-only" required>
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 peer-checked:bg-emerald-100">
                                        @if($icon === 'week')
                                            <svg class="h-4 w-4 text-slate-500 peer-checked:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @elseif($icon === 'month')
                                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-sm font-bold text-slate-900">{{ $label }}</span>
                                </div>
                                <p class="pl-10 text-xs text-slate-400">{{ $desc }}</p>
                                <div class="absolute top-3 right-3 h-4 w-4 rounded-full border-2 border-slate-300 bg-white peer-checked:border-emerald-500 peer-checked:bg-emerald-500 transition-all hidden peer-checked:flex items-center justify-center">
                                    <svg class="h-2.5 w-2.5 text-white" fill="currentColor" viewBox="0 0 12 12">
                                        <path d="M10 3L5 8.5 2 5.5"/>
                                    </svg>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Custom date range --}}
                    <div id="customDateFields" class="hidden grid grid-cols-2 gap-4 rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                        <div class="pb-field">
                            <label class="pb-label" for="date_from">From Date</label>
                            <input type="date" id="date_from" name="date_from" class="pb-input mt-1">
                        </div>
                        <div class="pb-field">
                            <label class="pb-label" for="date_to">To Date</label>
                            <input type="date" id="date_to" name="date_to" class="pb-input mt-1">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters section --}}
            <div class="pb-card overflow-hidden mt-4">
                <div class="border-b border-slate-100 px-6 py-4">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-4 w-1 rounded-full bg-slate-400"></div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Step 2 of 2 — Filters (optional)</p>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Refine your report</h2>
                </div>

                <div class="p-6 grid gap-5 sm:grid-cols-2">

                    {{-- Transaction type --}}
                    <div class="pb-field">
                        <label class="pb-label" for="type">Transaction Type</label>
                        <select name="type" id="type" class="pb-select mt-1">
                            <option value="">All Types</option>
                            <option value="income">Income only</option>
                            <option value="expense">Expenses only</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Filter by transaction direction</p>
                    </div>

                    {{-- Entry type --}}
                    <div class="pb-field">
                        <label class="pb-label" for="entry_type">Entry Classification</label>
                        <select name="entry_type" id="entry_type" class="pb-select mt-1">
                            <option value="">All Classifications</option>
                            <option value="credit_from_ceo">Credit from CEO</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Filter by entry source or type</p>
                    </div>

                    {{-- Category --}}
                    <div class="pb-field sm:col-span-2">
                        <label class="pb-label" for="category">Category</label>
                        <select name="category" id="category" class="pb-select mt-1">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Narrow report to a specific spending or income category</p>
                    </div>
                </div>

                {{-- Action --}}
                <div class="border-t border-slate-100 px-6 py-4 flex items-center justify-between gap-4 bg-slate-50/50">
                    <p class="text-xs text-slate-400">
                        Report opens in a new tab as a PDF document.
                    </p>
                    <button type="submit"
                            class="pb-btn pb-btn-md pb-btn-success text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generate &amp; Download PDF
                    </button>
                </div>
            </div>

        </form>

        {{-- ════ SEND VIA EMAIL ════ --}}
        @if (session('success'))
            <div class="animate-fade-in-up pb-card border-l-4 border-emerald-500 bg-emerald-50 px-5 py-4 mt-4 lg:col-span-2 flex items-start gap-3">
                <svg class="h-5 w-5 shrink-0 text-emerald-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
        @endif

        <form id="emailReportForm"
              action="{{ route('admin.finance.report-email') }}"
              method="POST"
              class="animate-fade-in-up delay-150 lg:col-span-2">
            @csrf

            {{-- Mirror the same period/filter fields as hidden inputs so they travel with the email form --}}
            <div id="emailMirrorFields"></div>

            <div class="pb-card overflow-hidden mt-4">
                <div class="border-b border-slate-100 px-6 py-4">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-4 w-1 rounded-full bg-pink-500"></div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Optional — Send by Email</p>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Email this report</h2>
                </div>

                <div class="p-6">
                    <div class="pb-field">
                        <label class="pb-label" for="email">Recipient email address</label>
                        <input type="email" id="email" name="email"
                               class="pb-input mt-1 @error('email') border-red-400 @enderror"
                               placeholder="e.g. finance@printbuka.com"
                               value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-400">
                            The PDF will be generated with the same period and filters selected above, then emailed as an attachment.
                        </p>
                    </div>
                </div>

                <div class="border-t border-slate-100 px-6 py-4 flex items-center justify-between gap-4 bg-slate-50/50">
                    <p class="text-xs text-slate-400">
                        Report will be sent immediately.
                    </p>
                    <button type="submit"
                            class="pb-btn pb-btn-md pb-btn-primary text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Send Report via Email
                    </button>
                </div>
            </div>
        </form>

        {{-- ════ SIDEBAR ════ --}}
        <aside class="animate-fade-in-up delay-200 space-y-4">

            {{-- Tips card --}}
            <div class="pb-card p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-900">Report contents</p>
                </div>
                <ul class="space-y-2.5 text-xs text-slate-500">
                    @foreach([
                        'Printbuka branded cover header with logo',
                        'Summary KPIs — income, expenses, net profit',
                        'Profit margin and income-to-expense ratio',
                        'Full itemised transaction ledger',
                        'Period totals row in the footer',
                        'Generation timestamp and preparer name',
                    ] as $tip)
                    <li class="flex items-start gap-2">
                        <svg class="h-3.5 w-3.5 shrink-0 mt-0.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $tip }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Period guide --}}
            <div class="pb-card p-5 space-y-3">
                <p class="text-sm font-bold text-slate-900">Period guide</p>
                <div class="space-y-2">
                    @foreach([
                        ['Weekly', 'Mon – Sun of the current week', 'bg-sky-100 text-sky-700'],
                        ['Monthly', 'First to last day of current month', 'bg-violet-100 text-violet-700'],
                        ['Custom', 'Any date range you specify', 'bg-amber-100 text-amber-700'],
                    ] as [$name, $desc, $cls])
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 inline-block rounded px-1.5 py-0.5 text-[10px] font-bold {{ $cls }} shrink-0">{{ $name }}</span>
                        <span class="text-xs text-slate-500">{{ $desc }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Finance link --}}
            <a href="{{ route('admin.finance.index') }}"
               class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                View Finance Dashboard
                <svg class="h-4 w-4 ml-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

        </aside>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios       = document.querySelectorAll('input[name="period"]');
    const customFields = document.getElementById('customDateFields');
    const fromInput    = document.getElementById('date_from');
    const toInput      = document.getElementById('date_to');

    function toggle() {
        const isCustom = document.querySelector('input[name="period"]:checked')?.value === 'custom';
        customFields.classList.toggle('hidden', !isCustom);
        if (isCustom && !fromInput.value) {
            const now   = new Date();
            const first = new Date(now.getFullYear(), now.getMonth(), 1);
            const last  = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            fromInput.value = first.toISOString().split('T')[0];
            toInput.value   = last.toISOString().split('T')[0];
        }
    }

    radios.forEach(r => r.addEventListener('change', toggle));
    toggle();

    // Mirror download-form fields into the email form before submit
    const downloadForm = document.querySelector('form[action*="report-download"]');
    const emailForm    = document.getElementById('emailReportForm');
    const mirrorDiv    = document.getElementById('emailMirrorFields');

    function syncMirrorFields() {
        mirrorDiv.innerHTML = '';
        const data = new FormData(downloadForm);
        for (const [key, val] of data.entries()) {
            if (key === '_token') continue;
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = key;
            inp.value = val;
            mirrorDiv.appendChild(inp);
        }
    }

    // Sync on email form submit so values are always current
    emailForm.addEventListener('submit', syncMirrorFields);
});
</script>
@endpush
