@extends('layouts.admin')

@section('title', 'Training Applicant')

@php
    $badgeClass = match ($application->status) {
        \App\Models\Training::STATUS_ACCEPTED => 'pb-badge-success',
        \App\Models\Training::STATUS_REJECTED => 'pb-badge-danger',
        default => 'pb-badge-warning',
    };
@endphp

@section('content')
    <section class="space-y-6">
        <div class="pb-page-header items-start">
            <div>
                <a href="{{ route('admin.training.index') }}" class="text-sm font-black text-cyan-700">Training Applications</a>
                <h1 class="pb-page-title mt-2 text-4xl">{{ $application->fullName() }}</h1>
                <p class="pb-page-subtitle">{{ $application->desired_skill }} · {{ $application->city_state }}</p>
            </div>
            <span class="pb-badge {{ $badgeClass }}">{{ $application->statusLabel() }}</span>
        </div>

        @if (session('status'))
            <div class="pb-alert pb-alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="pb-alert pb-alert-error">
                Please check the decision note and try again.
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <div class="pb-card p-6">
                    <h2 class="pb-section-title">Applicant details</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        @foreach ([
                            'Email' => $application->email,
                            'Phone / WhatsApp' => $application->phone_whatsapp,
                            'Date of birth' => $application->date_of_birth?->format('F j, Y'),
                            'Gender' => $application->gender ?: 'Not provided',
                            'Address' => $application->contact_address,
                            'City / State' => $application->city_state,
                            'Qualification' => $application->educational_qualification,
                            'Current status' => $application->employment_status ?: 'Not provided',
                            'Experience' => $application->experience_level ?: 'Not provided',
                            'Has laptop' => $application->has_laptop ? 'Yes' : 'No',
                            'Availability' => $application->availability,
                            'Referral source' => $application->referral_source ?: 'Not provided',
                        ] as $label => $value)
                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $label }}</p>
                                <p class="mt-1 text-sm font-bold leading-6 text-slate-800">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                    @if ($application->portfolio_url)
                        <a href="{{ $application->portfolio_url }}" target="_blank" rel="noopener noreferrer" class="pb-btn pb-btn-outline mt-5">Open Portfolio</a>
                    @endif
                </div>

                <div class="pb-card p-6">
                    <h2 class="pb-section-title">Motivation</h2>
                    <p class="mt-4 whitespace-pre-line text-sm font-semibold leading-7 text-slate-700">{{ $application->motivation }}</p>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="pb-card p-6">
                    <h2 class="pb-section-title">Decision</h2>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">Accepting or rejecting sends an email to {{ $application->email }}.</p>

                    @if ($application->decided_at)
                        <div class="mt-5 rounded-lg bg-slate-50 p-4">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500">Last decision</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">{{ $application->statusLabel() }} on {{ $application->decided_at->format('M j, Y g:i A') }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">By {{ $application->decidedBy?->displayName() ?? 'Unknown staff' }}</p>
                        </div>
                    @endif

                    @if (! $application->isPending())
                        <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-black text-slate-900">Decision locked</p>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">This application has already been {{ strtolower($application->statusLabel()) }}. The accept/reject actions are no longer available.</p>
                        </div>
                    @else
                    <form action="{{ route('admin.training.decide', $application) }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\Training::STATUS_ACCEPTED }}">
                        <div class="pb-field">
                            <label for="accept_note" class="pb-label">Acceptance note</label>
                            <textarea id="accept_note" name="decision_note" rows="4" data-rich-editor class="pb-textarea w-full" placeholder="Optional onboarding note for the applicant.">{{ old('decision_note') }}</textarea>
                        </div>
                        <button class="pb-btn pb-btn-success w-full">Accept Applicant</button>
                    </form>

                    <form action="{{ route('admin.training.decide', $application) }}" method="POST" class="mt-6 space-y-4 border-t border-slate-100 pt-6">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\Training::STATUS_REJECTED }}">
                        <div class="pb-field">
                            <label for="reject_note" class="pb-label">Rejection note</label>
                            <textarea id="reject_note" name="decision_note" rows="4" data-rich-editor class="pb-textarea w-full" placeholder="Optional polite feedback for the applicant.">{{ old('decision_note') }}</textarea>
                        </div>
                        <button class="pb-btn pb-btn-destructive w-full">Reject Applicant</button>
                    </form>
                    @endif
                </div>
            </aside>
        </div>
    </section>
@endsection
