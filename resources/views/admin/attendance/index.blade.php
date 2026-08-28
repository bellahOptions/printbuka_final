@extends('layouts.admin')
@section('title', 'Attendance | Printbuka')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div>
        <h1 class="text-2xl font-black text-slate-950">My Attendance</h1>
        @if ($location)
            <p class="text-sm text-slate-500 mt-1">Clock in/out from {{ $location->name }} — {{ $location->address }}.</p>
        @endif
    </div>

    <livewire:admin.attendance-clock />

    @php($myProfile = auth()->user()?->staffProfile)
    <details class="pb-card p-6">
        <summary class="cursor-pointer text-sm font-black text-slate-900">
            Work arrangement — {{ $myProfile?->workModeLabel() ?? 'Not set' }}
            @if ($myProfile?->work_mode === 'hybrid')
                <span class="font-normal text-slate-500">(onsite {{ $myProfile->onsiteDaysLabel() }})</span>
            @endif
        </summary>
        <form method="POST" action="{{ route('admin.staff.work-mode.update') }}" class="mt-4 space-y-4" data-work-mode-form>
            @csrf
            @include('admin._partials.work-mode-fields', ['profile' => $myProfile])
            <button type="submit" class="pb-btn pb-btn-outline w-full">Update work arrangement</button>
        </form>
    </details>

</div>
@endsection
