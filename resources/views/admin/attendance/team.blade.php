@extends('layouts.admin')
@section('title', 'Team Attendance | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Team Attendance</h1>
            <p class="text-sm text-slate-500 mt-1">Who's in, out, or missing today.</p>
        </div>
        <form method="GET" action="{{ route('admin.attendance.team') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="pb-input">
        </form>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    @if (auth()->user()?->canAdmin('attendance.manage'))
        <a href="{{ route('admin.attendance.location.edit') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">Location & shift settings →</a>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr class="text-xs font-black uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3.5 text-left">Staff</th>
                    <th class="px-5 py-3.5 text-left">Clock In</th>
                    <th class="px-5 py-3.5 text-left">Clock Out</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($staff as $member)
                    @php($record = $records->get($member->id))
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4 font-black text-slate-900">{{ $member->displayName() }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $record?->clock_in_at?->format('h:i A') ?? '—' }}
                            @if ($record?->clock_in_within_geofence === false)
                                <span class="pb-badge bg-amber-100 text-amber-800 ml-1 text-[10px]">off-site</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $record?->clock_out_at?->format('h:i A') ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if ($record)
                                <span class="pb-badge {{ $record->statusBadgeClass() }}">{{ $record->statusLabel() }}</span>
                                @if ($record->hasOvertime())
                                    <span class="pb-badge bg-purple-100 text-purple-800 text-[10px]">+{{ $record->overtimeLabel() }} OT</span>
                                @endif
                            @else
                                <span class="pb-badge bg-slate-100 text-slate-500">No record</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.attendance.show', $member) }}" class="text-sm font-black text-slate-700 hover:text-pink-600">History</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
