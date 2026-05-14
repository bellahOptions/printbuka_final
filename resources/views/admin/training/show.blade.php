@extends('layouts.admin')

@section('title', 'Training Applicant')

@php
    $badgeClass = match ($application->status) {
        \App\Models\Training::STATUS_ACCEPTED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        \App\Models\Training::STATUS_REJECTED => 'bg-pink-50 text-pink-700 border-pink-200',
        default => 'bg-amber-50 text-amber-700 border-amber-200',
    };
@endphp

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a href="{{ route('admin.training.index') }}" class="text-sm font-black text-cyan-700">Training Applications</a>
                <h1 class="mt-2 text-4xl font-black text-slate-950">{{ $application->fullName() }}</h1>
                <p class="mt-2 text-sm font-semibold text-slate-600">{{ $application->desired_skill }} · {{ $application->city_state }}</p>
            </div>
            <span class="inline-flex w-fit rounded-full border px-4 py-2 text-xs font-black uppercase {{ $badgeClass }}">{{ $application->statusLabel() }}</span>
        </div>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-md border border-pink-200 bg-pink-50 p-4 text-sm font-bold text-pink-800">
                Please check the decision note and try again.
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-slate-950">Applicant details</h2>
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
                        <a href="{{ $application->portfolio_url }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex rounded-md border border-slate-200 px-4 py-2 text-sm font-black text-pink-700 transition hover:border-pink-300">Open Portfolio</a>
                    @endif
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-slate-950">Motivation</h2>
                    <p class="mt-4 whitespace-pre-line text-sm font-semibold leading-7 text-slate-700">{{ $application->motivation }}</p>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-black text-slate-950">Decision</h2>
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
                        <div>
                            <label for="accept_note" class="text-sm font-black text-slate-800">Acceptance note</label>
                            <textarea id="accept_note" name="decision_note" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Optional onboarding note for the applicant.">{{ old('decision_note') }}</textarea>
                        </div>
                        <button class="min-h-11 w-full rounded-md bg-emerald-600 px-5 text-sm font-black text-white transition hover:bg-emerald-700">Accept Applicant</button>
                    </form>

                    <form action="{{ route('admin.training.decide', $application) }}" method="POST" class="mt-6 space-y-4 border-t border-slate-100 pt-6">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ \App\Models\Training::STATUS_REJECTED }}">
                        <div>
                            <label for="reject_note" class="text-sm font-black text-slate-800">Rejection note</label>
                            <textarea id="reject_note" name="decision_note" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Optional polite feedback for the applicant.">{{ old('decision_note') }}</textarea>
                        </div>
                        <button class="min-h-11 w-full rounded-md bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">Reject Applicant</button>
                    </form>
                    @endif
                </div>
            </aside>
        </div>
    </section>
@endsection
