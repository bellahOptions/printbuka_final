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

</div>
@endsection
