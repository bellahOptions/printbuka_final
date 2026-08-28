@extends('layouts.admin')
@section('title', $staff->displayName().' | Attendance | Printbuka')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">

    <div>
        <a href="{{ route('admin.attendance.team') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Team Attendance</a>
        <h1 class="text-2xl font-black text-slate-950 mt-2">{{ $staff->displayName() }}</h1>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    @if ($overtimeThisMonthMinutes > 0)
        <div class="rounded-xl border border-purple-200 bg-purple-50 p-4 text-sm font-bold text-purple-800">
            {{ intdiv($overtimeThisMonthMinutes, 60) }}h {{ $overtimeThisMonthMinutes % 60 }}m of overtime recorded this month.
        </div>
    @endif

    <div class="pb-card p-5">
        <p class="text-sm font-black text-slate-900">Manual entry</p>
        <p class="text-xs text-slate-500 mt-1 mb-4">
            Fallback for when {{ $staff->displayName() }} couldn't clock in through the app — off-site on a legitimate
            errand, geofence blocked, dead phone, or a forgotten punch. Enter the actual time worked directly;
            no location is recorded for a manual entry.
        </p>
        <form method="POST" action="{{ route('admin.attendance.manual-entry', $staff) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
            @csrf
            <label class="block">
                <span class="pb-label text-xs">Date</span>
                <input type="date" name="work_date" value="{{ old('work_date', now(config('app.business_timezone'))->toDateString()) }}" max="{{ now(config('app.business_timezone'))->toDateString() }}" class="pb-input" required>
            </label>
            <label class="block">
                <span class="pb-label text-xs">Clock in</span>
                <input type="time" name="clock_in_time" value="{{ old('clock_in_time') }}" class="pb-input">
            </label>
            <label class="block">
                <span class="pb-label text-xs">Clock out</span>
                <input type="time" name="clock_out_time" value="{{ old('clock_out_time') }}" class="pb-input">
            </label>
            <label class="block">
                <span class="pb-label text-xs">Status</span>
                <select name="status" class="pb-select">
                    @foreach (['present', 'late', 'absent', 'on_leave', 'half_day'] as $status)
                        <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="pb-btn pb-btn-primary">Save entry</button>
            <label class="block sm:col-span-2 lg:col-span-5">
                <span class="pb-label text-xs">Notes (optional)</span>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Why this was entered manually" class="pb-input">
            </label>
        </form>
        @error('clock_in_time')<p class="pb-field-error">{{ $message }}</p>@enderror
        @error('clock_out_time')<p class="pb-field-error">{{ $message }}</p>@enderror
        @error('work_date')<p class="pb-field-error">{{ $message }}</p>@enderror
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Date</th>
                    <th class="px-5 py-3.5 text-left">In / Out</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5 text-left">Correct</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($records as $record)
                    <tr>
                        <td class="px-5 py-4 text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($record->work_date)->format('D, M j Y') }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $record->clock_in_at?->format('h:i A') ?? '—' }} – {{ $record->clock_out_at?->format('h:i A') ?? '—' }}
                            @if ($record->clock_in_at && $record->corrected_by_id && $record->clock_in_within_geofence === null)
                                <p class="text-xs text-violet-700 mt-0.5">Manually entered by {{ $record->correctedBy?->displayName() }}</p>
                            @endif
                            @if ($record->flagged_reason)
                                <p class="text-xs text-amber-700 mt-0.5">{{ $record->flagged_reason }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="pb-badge {{ $record->statusBadgeClass() }}">{{ $record->statusLabel() }}</span>
                            @if ($record->hasOvertime())
                                <span class="pb-badge bg-purple-100 text-purple-800">+{{ $record->overtimeLabel() }} OT</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.attendance.correct', $record) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="pb-select text-xs">
                                    @foreach (['present', 'late', 'absent', 'on_leave', 'half_day'] as $status)
                                        <option value="{{ $status }}" @selected($record->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs font-black text-pink-600 hover:text-pink-800">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-slate-400 font-semibold">No attendance records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($records->hasPages())
            <div class="px-5 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @endif
    </div>

</div>
@endsection
