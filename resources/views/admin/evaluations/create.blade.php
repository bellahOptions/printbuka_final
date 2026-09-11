@extends('layouts.admin')
@section('title', 'Staff Evaluation | Printbuka')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.evaluations.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">← Back to Evaluations</a>
            <h1 class="pb-page-title">{{ isset($evaluation) ? 'Edit Evaluation' : 'New Evaluation' }}</h1>
            <p class="pb-page-subtitle">Monthly performance review</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="pb-alert pb-alert-error flex-col items-start">
            @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="pb-card pb-card-content">
        <form method="POST" action="{{ isset($evaluation) ? route('admin.evaluations.update', $evaluation) : route('admin.evaluations.store') }}">
            @csrf
            @if (isset($evaluation)) @method('PUT') @endif

            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Staff Member <span class="text-brand-600">*</span></label>
                        <select name="staff_id" required class="pb-select w-full">
                            <option value="">Select staff...</option>
                            @foreach ($staffList as $s)
                                <option value="{{ $s->id }}" @selected(old('staff_id', $evaluation->staff_id ?? request('staff_id')) == $s->id)>{{ $s->displayName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Evaluation Period <span class="text-brand-600">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="period_month" required class="pb-select">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected(old('period_month', $evaluation->period_month ?? now()->month) == $m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endforeach
                            </select>
                            <select name="period_year" required class="pb-select">
                                @foreach (range(now()->year, now()->year - 3) as $y)
                                    <option value="{{ $y }}" @selected(old('period_year', $evaluation->period_year ?? now()->year) == $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Rating categories --}}
                <div>
                    <p class="pb-label text-brand-600">Performance Ratings (1 = Poor, 5 = Excellent)</p>
                    <div class="space-y-4">
                        @php
                        $ratingFields = [
                            'punctuality_rating' => 'Punctuality & Attendance',
                            'quality_of_work_rating' => 'Quality of Work',
                            'teamwork_rating' => 'Teamwork & Collaboration',
                            'communication_rating' => 'Communication',
                            'initiative_rating' => 'Initiative & Problem Solving',
                        ];
                        @endphp
                        @foreach ($ratingFields as $field => $label)
                        <div class="flex items-center justify-between gap-4">
                            <label class="text-sm font-semibold text-slate-700 w-56 shrink-0">{{ $label }}</label>
                            <div class="flex gap-2">
                                @foreach (range(1, 5) as $star)
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="{{ $field }}" value="{{ $star }}" @checked(old($field, $evaluation->$field ?? null) == $star) class="sr-only peer" required>
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-300 text-sm font-semibold text-slate-600 peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 cursor-pointer hover:bg-slate-100 transition select-none">{{ $star }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-4">
                            <label class="text-sm font-semibold text-slate-900 w-56 shrink-0">Overall Rating</label>
                            <div class="flex gap-2">
                                @foreach (range(1, 5) as $star)
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="overall_rating" value="{{ $star }}" @checked(old('overall_rating', $evaluation->overall_rating ?? null) == $star) class="sr-only peer" required>
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-300 text-sm font-semibold text-slate-600 peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 cursor-pointer hover:bg-brand-50 transition select-none">{{ $star }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-field">
                    <label class="pb-label">Strengths / Commendations</label>
                    <textarea name="strengths" rows="3" data-rich-editor class="pb-textarea w-full">{{ old('strengths', $evaluation->strengths ?? '') }}</textarea>
                </div>

                <div class="pb-field">
                    <label class="pb-label">Areas for Improvement</label>
                    <textarea name="areas_for_improvement" rows="3" data-rich-editor class="pb-textarea w-full">{{ old('areas_for_improvement', $evaluation->areas_for_improvement ?? '') }}</textarea>
                </div>

                <div class="pb-field">
                    <label class="pb-label">Additional Comments</label>
                    <textarea name="comments" rows="2" data-rich-editor class="pb-textarea w-full">{{ old('comments', $evaluation->comments ?? '') }}</textarea>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="pb-btn pb-btn-md pb-btn-ink">Save Evaluation</button>
                    <a href="{{ route('admin.evaluations.index') }}" class="pb-btn pb-btn-md pb-btn-outline">Cancel</a>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
