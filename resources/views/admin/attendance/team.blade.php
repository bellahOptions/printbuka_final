@extends('layouts.admin')
@section('title', 'Team Attendance | Printbuka')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="pb-page-header">
        <div>
            <h1 class="pb-page-title">Team Attendance</h1>
            <p class="pb-page-subtitle">Who's in, out, or missing today.</p>
        </div>
        <form method="GET" action="{{ route('admin.attendance.team') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="pb-input">
        </form>
    </div>

    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif

    @if (auth()->user()?->canAdmin('attendance.manage'))
        <a href="{{ route('admin.attendance.location.edit') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">Location & shift settings →</a>
    @endif

    <div class="pb-table-wrapper">
        <table class="pb-table">
            <thead>
                <tr>
                    <th>Staff</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staff as $member)
                    @php($record = $records->get($member->id))
                    <tr>
                        <td class="font-black text-slate-900">{{ $member->displayName() }}</td>
                        <td class="text-sm text-slate-600">
                            {{ $record?->clock_in_at?->format('h:i A') ?? '—' }}
                            @if ($record?->clock_in_within_geofence === false)
                                <span class="pb-badge pb-badge-warning ml-1 text-[10px]">off-site</span>
                            @endif
                        </td>
                        <td class="text-sm text-slate-600">{{ $record?->clock_out_at?->format('h:i A') ?? '—' }}</td>
                        <td>
                            @if ($record)
                                <span class="pb-badge {{ $record->statusBadgeClass() }}">{{ $record->statusLabel() }}</span>
                                @if ($record->hasOvertime())
                                    <span class="pb-badge pb-badge-purple text-[10px]">+{{ $record->overtimeLabel() }} OT</span>
                                @endif
                            @else
                                <span class="pb-badge pb-badge-secondary">No record</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.attendance.show', $member) }}" class="text-sm font-black text-slate-700 hover:text-pink-600">History</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
