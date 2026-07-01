@extends('layouts.admin')
@section('title', $staffMember->displayName().' — Staff Profile | Printbuka')

@section('content')
@php($viewer = auth()->user())
@php($canManageKyc = $viewer->canAdmin('staff.kyc') || $viewer->canAdmin('*'))
@php($isSelf = $viewer->id === $staffMember->id)
@php($canEdit = $canManageKyc || $isSelf)

<div class="mx-auto max-w-5xl space-y-6">

    {{-- Hero --}}
    <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-8 text-white shadow-xl">
        <a href="{{ route('admin.staff.index') }}" class="text-sm font-black text-cyan-300 hover:text-cyan-200">← Back to Staff</a>
        <div class="mt-4 flex items-center gap-5">
            <img src="{{ $staffMember->profilePhotoUrl() }}" alt="{{ $staffMember->displayName() }}" class="h-20 w-20 rounded-2xl object-cover border-2 border-white/20">
            <div>
                <h1 class="text-3xl font-black">{{ $staffMember->displayName() }}</h1>
                <p class="mt-1 text-sm text-slate-300">{{ $staffMember->email }} · {{ ucwords(str_replace('_', ' ', $staffMember->role)) }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @if (($profile->kyc_status ?? 'pending') === 'approved')
                        <span class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-black">KYC Approved ✓</span>
                    @elseif (($profile->kyc_status ?? 'pending') === 'correction_requested')
                        <span class="rounded-full bg-amber-500 px-3 py-1 text-xs font-black">Correction Requested</span>
                    @else
                        <span class="rounded-full bg-slate-500 px-3 py-1 text-xs font-black">KYC Pending — {{ $profile->completionPercentage() }}%</span>
                    @endif
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">{{ $staffMember->department ?? 'No Department' }}</span>
                    <span class="rounded-full {{ $staffMember->employment_status === 'active' ? 'bg-emerald-600' : 'bg-pink-700' }} px-3 py-1 text-xs font-black">{{ $staffMember->employmentStatusLabel() }}</span>
                </div>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
            @foreach ($errors->all() as $e)<p class="text-sm font-semibold text-pink-700">{{ $e }}</p>@endforeach
        </div>
    @endif

    {{-- Quick Stats --}}
    <div class="grid gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <p class="text-2xl font-black text-slate-900">{{ $queries->count() }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mt-1">Queries</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <p class="text-2xl font-black text-slate-900">{{ $evaluations->count() }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mt-1">Evaluations</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <p class="text-2xl font-black {{ $latestSalary ? 'text-emerald-700' : 'text-slate-400' }}">
                {{ $latestSalary ? '₦'.number_format($latestSalary->netSalary(), 0) : 'Not set' }}
            </p>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mt-1">Net Salary</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
            <p class="text-2xl font-black text-slate-900">{{ $payslips->count() }}</p>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500 mt-1">Payslips</p>
        </div>
    </div>

    {{-- KYC Review Panel (HR / Super Admin only) --}}
    @if ($canManageKyc)
    <div class="rounded-2xl border {{ ($profile->kyc_status ?? 'pending') === 'approved' ? 'border-emerald-200 bg-emerald-50' : (($profile->kyc_status ?? 'pending') === 'correction_requested' ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-white') }} p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
            <div>
                <h2 class="text-lg font-black text-slate-950">KYC Review</h2>
                <p class="text-xs font-semibold text-slate-500 mt-1">Approve the bio-data or request corrections — staff is notified either way</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="rounded-full px-3 py-1 text-xs font-black {{ $profile->kycStatusBadgeClass() }}">
                    {{ $profile->kycStatusLabel() }}
                </span>
                @if ($profile->kyc_reviewed_at)
                    <span class="text-xs text-slate-400">
                        Reviewed {{ $profile->kyc_reviewed_at->diffForHumans() }}
                        @if ($profile->reviewedBy) by {{ $profile->reviewedBy->displayName() }} @endif
                    </span>
                @endif
            </div>
        </div>

        @if (($profile->kyc_status ?? 'pending') === 'correction_requested' && $profile->kyc_review_notes)
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-black uppercase tracking-wide text-amber-700 mb-1">Previous Correction Notes</p>
                <p class="text-sm text-amber-900">{{ $profile->kyc_review_notes }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.staff.kyc-review', $staffMember) }}" id="kyc-review-form">
            @csrf
            <input type="hidden" name="kyc_action" id="kyc_action_input" value="">

            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">
                    Notes / Correction Instructions <span class="text-slate-400 normal-case font-normal">(optional for approval, recommended for corrections)</span>
                </label>
                <textarea name="kyc_notes" rows="3"
                    placeholder="E.g. Please update your bank account number and next-of-kin address."
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none">{{ old('kyc_notes', ($profile->kyc_status ?? 'pending') === 'correction_requested' ? $profile->kyc_review_notes : '') }}</textarea>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="button"
                    onclick="document.getElementById('kyc_action_input').value='approve'; if(confirm('Approve KYC for {{ $staffMember->displayName() }}? Staff will be notified.')) document.getElementById('kyc-review-form').submit();"
                    class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-black text-white hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Approve KYC
                </button>
                <button type="button"
                    onclick="document.getElementById('kyc_action_input').value='request_correction'; if(confirm('Request corrections from {{ $staffMember->displayName() }}? Staff will be notified by email.')) document.getElementById('kyc-review-form').submit();"
                    class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-black text-white hover:bg-amber-600 flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Request Correction
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Bio-Data Form --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-black text-slate-950">Staff Employment Bio-Data</h2>
                <p class="text-xs font-semibold text-slate-500 mt-1">Compulsory KYC — all fields should be completed</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.staff.profile.update', $staffMember) }}">
            @csrf @method('PUT')

            {{-- Personal Information --}}
            <p class="text-xs font-black uppercase tracking-wide text-pink-600 mb-4 mt-2">Personal Information</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Last Name</label>
                    <input value="{{ $staffMember->last_name }}" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">First Name</label>
                    <input value="{{ $staffMember->first_name }}" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Other Names</label>
                    <input type="text" name="other_names" value="{{ old('other_names', $profile->other_names) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Designation / Job Title</label>
                    <input type="text" name="designation" value="{{ old('designation', $profile->designation) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Date of Employment</label>
                    <input type="date" name="date_of_employment" value="{{ old('date_of_employment', $profile->date_of_employment?->format('Y-m-d')) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Date of Birth</label>
                    <input value="{{ $staffMember->date_of_birth?->format('Y-m-d') }}" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Sex</label>
                    <select name="sex" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                        <option value="">Select</option>
                        <option value="Male" @selected(old('sex', $profile->sex) === 'Male')>Male</option>
                        <option value="Female" @selected(old('sex', $profile->sex) === 'Female')>Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Marital Status</label>
                    <select name="marital_status" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                        <option value="">Select</option>
                        @foreach (['Single', 'Married', 'Separated', 'Divorced'] as $ms)
                            <option @selected(old('marital_status', $profile->marital_status) === $ms)>{{ $ms }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">State of Origin</label>
                    <input type="text" name="state_of_origin" value="{{ old('state_of_origin', $profile->state_of_origin) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Local Govt. Area</label>
                    <input type="text" name="local_govt_area" value="{{ old('local_govt_area', $profile->local_govt_area) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Mobile Telephone</label>
                    <input value="{{ $staffMember->phone }}" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Home Telephone</label>
                    <input type="text" name="home_telephone" value="{{ old('home_telephone', $profile->home_telephone) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">E-mail</label>
                    <input value="{{ $staffMember->email }}" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Present Address</label>
                    <textarea name="present_address" rows="2" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">{{ old('present_address', $profile->present_address) }}</textarea>
                </div>
            </div>

            {{-- Next of Kin --}}
            <p class="text-xs font-black uppercase tracking-wide text-pink-600 mt-8 mb-4">Next of Kin</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Next of Kin's Name</label>
                    <input type="text" name="next_of_kin_name" value="{{ old('next_of_kin_name', $profile->next_of_kin_name) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Relationship</label>
                    <input type="text" name="next_of_kin_relationship" value="{{ old('next_of_kin_relationship', $profile->next_of_kin_relationship) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Next of Kin's Home Address</label>
                    <textarea name="next_of_kin_home_address" rows="2" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">{{ old('next_of_kin_home_address', $profile->next_of_kin_home_address) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Next of Kin's Office Address</label>
                    <textarea name="next_of_kin_office_address" rows="2" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none disabled:bg-slate-50">{{ old('next_of_kin_office_address', $profile->next_of_kin_office_address) }}</textarea>
                </div>
            </div>

            {{-- Post Held --}}
            <p class="text-xs font-black uppercase tracking-wide text-pink-600 mt-8 mb-4">Post / Position</p>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Post Held</label>
                    <input type="text" name="post_held" value="{{ old('post_held', $profile->post_held) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Post Telephone</label>
                    <input type="text" name="post_telephone" value="{{ old('post_telephone', $profile->post_telephone) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Post E-mail</label>
                    <input type="email" name="post_email" value="{{ old('post_email', $profile->post_email) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                </div>
            </div>

            {{-- Banking / Financial --}}
            <p class="text-xs font-black uppercase tracking-wide text-pink-600 mt-8 mb-4">Banking & Financial Details</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Account Number</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $profile->bank_account_number) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Pension PIN (PFA)</label>
                    <input type="text" name="pension_pin" value="{{ old('pension_pin', $profile->pension_pin) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Tax ID (TIN)</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $profile->tax_id) }}" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1">Emergency Contact Notes</label>
                    <textarea name="emergency_contact_notes" rows="2" @disabled(!$canEdit) class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none disabled:bg-slate-50">{{ old('emergency_contact_notes', $profile->emergency_contact_notes) }}</textarea>
                </div>
            </div>

            @if ($canEdit)
                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-black text-white hover:bg-slate-700">Save Bio-Data</button>
                    @if (! $profile->isComplete())
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="mark_kyc_complete" value="1" class="rounded">
                            Mark as KYC Complete
                        </label>
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- Queries Summary --}}
    @if ($queries->isNotEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-slate-950">Queries</h2>
            @if ($canManageKyc)
                <a href="{{ route('admin.staff-queries.create', ['staff_id' => $staffMember->id]) }}" class="rounded-xl bg-pink-600 px-4 py-2 text-sm font-black text-white hover:bg-pink-700">Issue Query</a>
            @endif
        </div>
        <div class="space-y-3">
            @foreach ($queries as $q)
            <a href="{{ route('admin.staff-queries.show', $q) }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 hover:border-pink-200 hover:bg-pink-50/30 transition">
                <div>
                    <p class="text-sm font-black text-slate-900">{{ $q->query_number }} · {{ $q->subject }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $q->typeLabel() }} · {{ $q->query_date->format('M j, Y') }}</p>
                </div>
                <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $q->statusBadgeClass() }}">{{ ucfirst($q->status) }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @else
    @if ($canManageKyc)
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex items-center justify-between">
        <p class="text-sm font-semibold text-slate-500">No queries on record.</p>
        <a href="{{ route('admin.staff-queries.create', ['staff_id' => $staffMember->id]) }}" class="rounded-xl bg-pink-600 px-4 py-2 text-sm font-black text-white hover:bg-pink-700">Issue Query</a>
    </div>
    @endif
    @endif

    {{-- Evaluations Summary --}}
    @if ($evaluations->isNotEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-slate-950">Evaluations</h2>
            @if ($canManageKyc)
                <a href="{{ route('admin.evaluations.create', ['staff_id' => $staffMember->id]) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 hover:bg-slate-50">+ New Evaluation</a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200 text-xs font-black uppercase tracking-wide text-slate-500">
                    <tr><th class="px-3 py-2 text-left">Period</th><th class="px-3 py-2 text-left">Overall</th><th class="px-3 py-2 text-left">Avg Rating</th><th class="px-3 py-2 text-left">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($evaluations->take(6) as $ev)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2 font-semibold">{{ $ev->periodLabel() }}</td>
                        <td class="px-3 py-2">{{ $ev->ratingStars($ev->overall_rating) }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ number_format($ev->averageRating(), 1) }}/5</td>
                        <td class="px-3 py-2"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-black text-slate-700">{{ ucfirst($ev->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Payslips --}}
    @if ($payslips->isNotEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-black text-slate-950 mb-4">Recent Payslips</h2>
        <div class="space-y-2">
            @foreach ($payslips as $slip)
            <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                <div>
                    <p class="text-sm font-black text-slate-900">{{ $slip->payrollRun?->periodLabel() }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">Net: ₦{{ number_format($slip->net_salary, 2) }}</p>
                </div>
                <a href="{{ route('admin.payroll.payslip.download', $slip) }}" class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-black text-slate-700 hover:bg-slate-50">Download PDF</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
