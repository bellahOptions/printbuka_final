@extends('layouts.admin')
@section('title', $evaluation->periodLabel().' Evaluation | Printbuka')

@section('content')
@php
    $viewer = auth()->user();
    $isSelf = $viewer->id === $evaluation->staff_id;
@endphp

<div class="mx-auto max-w-2xl space-y-6">

    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.evaluations.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">← Back to Evaluations</a>
            <div class="mt-3 flex items-center gap-4">
                <h1 class="pb-page-title">{{ $evaluation->periodLabel() }}</h1>
                <span class="pb-badge {{ $evaluation->status === 'acknowledged' ? 'pb-badge-success' : 'pb-badge-secondary' }}">{{ ucfirst($evaluation->status) }}</span>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif

    <div class="pb-card pb-card-content space-y-6">

        {{-- Staff header --}}
        <div class="flex items-center gap-3">
            <img src="{{ $evaluation->staff?->profilePhotoUrl() }}" class="h-12 w-12 rounded-2xl object-cover" alt="">
            <div>
                <p class="font-semibold text-slate-900">{{ $evaluation->staff?->displayName() }}</p>
                <p class="text-xs text-slate-500">Evaluated by {{ $evaluation->evaluatedBy?->displayName() }}</p>
            </div>
        </div>

        {{-- Ratings --}}
        @php
        $cats = [
            'Punctuality & Attendance' => $evaluation->punctuality_rating,
            'Quality of Work' => $evaluation->quality_of_work_rating,
            'Teamwork & Collaboration' => $evaluation->teamwork_rating,
            'Communication' => $evaluation->communication_rating,
            'Initiative & Problem Solving' => $evaluation->initiative_rating,
        ];
        @endphp
        <div>
            <p class="pb-label text-brand-600 mb-3">Performance Ratings</p>
            <div class="space-y-3">
                @foreach ($cats as $cat => $rating)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-700">{{ $cat }}</span>
                    <div class="flex items-center gap-2">
                        @if ($rating !== null)
                            <span class="text-base">{{ $evaluation->ratingStars($rating) }}</span>
                            <span class="text-xs font-semibold text-slate-500">{{ $rating }}/5</span>
                        @else
                            <span class="text-xs font-semibold text-slate-400">Not rated</span>
                        @endif
                    </div>
                </div>
                @endforeach
                <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                    <span class="text-sm font-semibold text-slate-900">Overall Rating</span>
                    <div class="flex items-center gap-2">
                        <span class="text-base">{{ $evaluation->ratingStars($evaluation->overall_rating) }}</span>
                        <span class="text-xs font-semibold text-brand-600">{{ $evaluation->overall_rating }}/5</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400">Average (all categories)</span>
                    <span class="text-xs font-semibold text-slate-700">{{ number_format($evaluation->averageRating(), 2) }}/5</span>
                </div>
            </div>
        </div>

        @if ($evaluation->strengths)
        <div>
            <p class="pb-label mb-2">Strengths / Commendations</p>
            <div class="pb-alert pb-alert-success">{{ $evaluation->strengths }}</div>
        </div>
        @endif

        @if ($evaluation->areas_for_improvement)
        <div>
            <p class="pb-label mb-2">Areas for Improvement</p>
            <div class="pb-alert pb-alert-warning">{{ $evaluation->areas_for_improvement }}</div>
        </div>
        @endif

        @if ($evaluation->comments)
        <div>
            <p class="pb-label mb-2">Additional Comments</p>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-800 leading-relaxed">{{ $evaluation->comments }}</div>
        </div>
        @endif

        @if ($evaluation->staff_acknowledged)
        <div class="pb-alert pb-alert-success">
            Acknowledged by staff on {{ $evaluation->updated_at->format('F j, Y') }}
        </div>
        @elseif ($isSelf && $evaluation->status !== 'acknowledged')
        <form method="POST" action="{{ route('admin.evaluations.acknowledge', $evaluation) }}">
            @csrf
            <button type="submit" class="pb-btn pb-btn-md pb-btn-success w-full">Acknowledge this Evaluation</button>
        </form>
        @endif

    </div>

</div>
@endsection
