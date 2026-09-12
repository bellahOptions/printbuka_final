@extends('layouts.admin')
@section('title', 'Issue Staff Query | Printbuka')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div class="pb-page-header">
        <div>
            <a href="{{ route('admin.staff-queries.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Queries</a>
            <h1 class="pb-page-title mt-3">Issue Staff Query</h1>
            <p class="pb-page-subtitle">Issue a formal disciplinary query to a staff member</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="pb-alert pb-alert-error">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pb-card p-6">
        <form method="POST" action="{{ route('admin.staff-queries.store') }}">
            @csrf

            <div class="space-y-5">
                <div class="pb-field">
                    <label class="pb-label">Staff Member <span class="text-pink-600">*</span></label>
                    <select name="staff_id" required class="pb-select w-full">
                        <option value="">Select staff member...</option>
                        @foreach ($staffList as $s)
                            <option value="{{ $s->id }}" @selected(old('staff_id', request('staff_id')) == $s->id)>{{ $s->displayName() }} ({{ ucwords(str_replace('_', ' ', $s->role)) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">Query Type <span class="text-pink-600">*</span></label>
                        <select name="query_type" required class="pb-select w-full">
                            <option value="">Select type...</option>
                            @foreach (\App\Models\StaffQuery::$types as $t)
                                <option @selected(old('query_type') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">Query Date <span class="text-pink-600">*</span></label>
                        <input type="date" name="query_date" value="{{ old('query_date', now()->format('Y-m-d')) }}" required class="pb-input w-full">
                    </div>
                </div>

                <div class="pb-field">
                    <label class="pb-label">Subject <span class="text-pink-600">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="Brief subject of the query" class="pb-input w-full">
                </div>

                <div class="pb-field">
                    <label class="pb-label">Query Description <span class="text-pink-600">*</span></label>
                    <textarea name="description" rows="5" required data-rich-editor placeholder="Describe the infraction or issue in detail..." class="pb-textarea w-full">{{ old('description') }}</textarea>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="pb-field">
                        <label class="pb-label">CC (optional)</label>
                        <input type="text" name="cc_emails" value="{{ old('cc_emails') }}" placeholder="hr@printbuka.com, manager@printbuka.com" class="pb-input w-full">
                        <p class="text-xs text-slate-400 mt-1">Comma-separated email addresses.</p>
                    </div>
                    <div class="pb-field">
                        <label class="pb-label">BCC (optional)</label>
                        <input type="text" name="bcc_emails" value="{{ old('bcc_emails') }}" placeholder="records@printbuka.com" class="pb-input w-full">
                        <p class="text-xs text-slate-400 mt-1">Comma-separated email addresses.</p>
                    </div>
                </div>

                <div class="pb-field">
                    <label class="pb-label">Response Due Date</label>
                    <input type="date" name="response_due_date" value="{{ old('response_due_date') }}" class="pb-input w-full">
                    <p class="text-xs text-slate-400 mt-1">Leave blank if no formal response deadline.</p>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="pb-btn pb-btn-primary">Issue Query &amp; Notify Staff</button>
                    <a href="{{ route('admin.staff-queries.index') }}" class="pb-btn pb-btn-outline">Cancel</a>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
