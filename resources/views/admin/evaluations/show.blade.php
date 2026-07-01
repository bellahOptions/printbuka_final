@extends('layouts.admin')
@section('title', $evaluation->periodLabel().' Evaluation | Printbuka')

@section('content')
@php($viewer = auth()->user())
@php($isSelf = $viewer->id === $evaluation->staff_id)

<div class="mx-auto max-w-2xl space-y-6">

    <div>
        <a href="{{ route('admin.evaluations.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Evaluations</a>
        <div class="mt-3 flex items-center gap-4">
            <h1 class="text-2xl font-black text-slate-950">{{ $evaluation->periodLabel() }}</h1>
            <span class="rounded-full px-3 py-1 text-xs font-black {{ $evaluation->status === 'acknowledged' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($evaluation->status) }}</span>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">

        {{-- Staff header --}}
        <div class="flex items-center gap-3">
            <img src="{{ $evaluation->staff?->profilePhotoUrl() }}" class="h-12 w-12 rounded-2xl object-cover" alt="">
            <div>
                <p class="font-black text-slate-950">{{ $evaluation->staff?->displayName() }}</p>
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
            <p class="text-xs font-black uppercase tracking-wide text-pink-600 mb-3">Performance Ratings</p>
            <div class="space-y-3">
                @foreach ($cats as $cat => $rating)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-700">{{ $cat }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-base">{{ $evaluation->ratingStars($rating) }}</span>
                        <span class="text-xs font-black text-slate-500">{{ $rating }}/5</span>
                    </div>
                </div>
                @endforeach
                <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                    <span class="text-sm font-black text-slate-900">Overall Rating</span>
                    <div class="flex items-center gap-2">
                        <span class="text-base">{{ $evaluation->ratingStars($evaluation->overall_rating) }}</span>
                        <span class="text-xs font-black text-pink-600">{{ $evaluation->overall_rating }}/5</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400">Average (all categories)</span>
                    <span class="text-xs font-black text-slate-700">{{ number_format($evaluation->averageRating(), 2) }}/5</span>
                </div>
            </div>
        </div>

        @if ($evaluation->strengths)
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-2">Strengths / Commendations</p>
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-slate-800 leading-relaxed">{{ $evaluation->strengths }}</div>
        </div>
        @endif

        @if ($evaluation->areas_for_improvement)
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-2">Areas for Improvement</p>
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-slate-800 leading-relaxed">{{ $evaluation->areas_for_improvement }}</div>
        </div>
        @endif

        @if ($evaluation->comments)
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-slate-400 mb-2">Additional Comments</p>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-800 leading-relaxed">{{ $evaluation->comments }}</div>
        </div>
        @endif

        @if ($evaluation->staff_acknowledged)
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
            <p class="text-sm font-black text-emerald-800">Acknowledged by staff on {{ $evaluation->updated_at->format('F j, Y') }}</p>
        </div>
        @elseif ($isSelf && $evaluation->status !== 'acknowledged')
        <form method="POST" action="{{ route('admin.evaluations.acknowledge', $evaluation) }}">
            @csrf
            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-6 py-3 text-sm font-black text-white hover:bg-emerald-700">Acknowledge this Evaluation</button>
        </form>
        @endif

    </div>

</div>
@endsection
