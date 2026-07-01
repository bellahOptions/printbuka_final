@extends('layouts.admin')
@section('title', 'Issue Staff Query | Printbuka')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div>
        <a href="{{ route('admin.staff-queries.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Queries</a>
        <h1 class="text-2xl font-black text-slate-950 mt-3">Issue Staff Query</h1>
        <p class="text-sm text-slate-500 mt-1">Issue a formal disciplinary query to a staff member</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4">
            @foreach ($errors->all() as $e)<p class="text-sm font-semibold text-pink-700">{{ $e }}</p>@endforeach
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.staff-queries.store') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Staff Member <span class="text-pink-600">*</span></label>
                    <select name="staff_id" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none">
                        <option value="">Select staff member...</option>
                        @foreach ($staffMembers as $s)
                            <option value="{{ $s->id }}" @selected(old('staff_id', request('staff_id')) == $s->id)>{{ $s->displayName() }} ({{ ucwords(str_replace('_', ' ', $s->role)) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Query Type <span class="text-pink-600">*</span></label>
                        <select name="query_type" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none">
                            <option value="">Select type...</option>
                            @foreach (\App\Models\StaffQuery::$types as $t)
                                <option @selected(old('query_type') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Query Date <span class="text-pink-600">*</span></label>
                        <input type="date" name="query_date" value="{{ old('query_date', now()->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Subject <span class="text-pink-600">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="Brief subject of the query" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Query Description <span class="text-pink-600">*</span></label>
                    <textarea name="description" rows="5" required placeholder="Describe the infraction or issue in detail..." class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wide text-slate-500 mb-1.5">Response Due Date</label>
                    <input type="date" name="response_due_date" value="{{ old('response_due_date') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100 focus:outline-none">
                    <p class="text-xs text-slate-400 mt-1">Leave blank if no formal response deadline.</p>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" class="rounded-xl bg-pink-600 px-6 py-3 text-sm font-black text-white hover:bg-pink-700 shadow-sm">Issue Query &amp; Notify Staff</button>
                    <a href="{{ route('admin.staff-queries.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
