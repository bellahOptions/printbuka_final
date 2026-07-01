@extends('layouts.admin')
@section('title', 'Staff Evaluation | Printbuka')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div>
        <a href="{{ route('admin.evaluations.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Evaluations</a>
        <h1 class="text-2xl font-black text-slate-950 mt-3">{{ isset($evaluation) ? 'Edit Evaluation' : 'New Evaluation' }}</h1>
        <p class="text-sm text-slate-500 mt-1">Monthly performance review</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
            @foreach ($errors->all() as $e)<p class="text-sm font-semibold text-pink-700">{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ isset($evaluation) ? route('admin.evaluations.update', $evaluation) : route('admin.evaluations.store') }}">
            @csrf
            @if (isset($evaluation)) @method('PUT') @endif

            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Staff Member <span class="text-pink-600">*</span></label>
                        <select name="staff_id" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                            <option value="">Select staff...</option>
                            @foreach ($staffList as $s)
                                <option value="{{ $s->id }}" @selected(old('staff_id', $evaluation->staff_id ?? request('staff_id')) == $s->id)>{{ $s->displayName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Evaluation Period <span class="text-pink-600">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="period_month" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected(old('period_month', $evaluation->period_month ?? now()->month) == $m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endforeach
                            </select>
                            <select name="period_year" required class="rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-pink-400 focus:outline-none">
                                @foreach (range(now()->year, now()->year - 3) as $y)
                                    <option value="{{ $y }}" @selected(old('period_year', $evaluation->period_year ?? now()->year) == $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Rating categories --}}
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-pink-600 mb-4">Performance Ratings (1 = Poor, 5 = Excellent)</p>
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
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-300 text-sm font-black text-slate-600 peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 cursor-pointer hover:bg-slate-100 transition select-none">{{ $star }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-4">
                            <label class="text-sm font-black text-slate-900 w-56 shrink-0">Overall Rating</label>
                            <div class="flex gap-2">
                                @foreach (range(1, 5) as $star)
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="overall_rating" value="{{ $star }}" @checked(old('overall_rating', $evaluation->overall_rating ?? null) == $star) class="sr-only peer" required>
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-300 text-sm font-black text-slate-600 peer-checked:bg-pink-600 peer-checked:text-white peer-checked:border-pink-600 cursor-pointer hover:bg-pink-50 transition select-none">{{ $star }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Strengths / Commendations</label>
                    <textarea name="strengths" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">{{ old('strengths', $evaluation->strengths ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Areas for Improvement</label>
                    <textarea name="areas_for_improvement" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">{{ old('areas_for_improvement', $evaluation->areas_for_improvement ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Additional Comments</label>
                    <textarea name="comments" rows="2" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:outline-none">{{ old('comments', $evaluation->comments ?? '') }}</textarea>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-black text-white hover:bg-slate-700">Save Evaluation</button>
                    <a href="{{ route('admin.evaluations.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
