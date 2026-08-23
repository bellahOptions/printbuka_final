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
