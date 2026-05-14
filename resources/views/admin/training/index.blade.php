@extends('layouts.admin')

@section('title', 'Training Applications')

@php
    $badgeClass = function (string $status): string {
        return match ($status) {
            \App\Models\Training::STATUS_ACCEPTED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            \App\Models\Training::STATUS_REJECTED => 'bg-pink-50 text-pink-700 border-pink-200',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    };
@endphp

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">PGTP Applications</p>
                <h1 class="mt-2 text-4xl font-black text-slate-950">Training submissions.</h1>
                <p class="mt-2 text-sm font-semibold text-slate-600">Review applicants and send acceptance or rejection decisions.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([['Total', $stats['total'], 'text-slate-950'], ['Pending', $stats['pending'], 'text-amber-700'], ['Accepted', $stats['accepted'], 'text-emerald-700'], ['Rejected', $stats['rejected'], 'text-pink-700']] as [$label, $value, $class])
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-black {{ $class }}">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.training.index') }}" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_220px_220px_auto]">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search name, email, phone..." class="min-h-11 rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
            <select name="status" class="min-h-11 rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                <option value="">All statuses</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="skill" class="min-h-11 rounded-md border border-slate-200 px-4 text-sm font-semibold outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                <option value="">All skills</option>
                @foreach ($skills as $skillOption)
                    <option value="{{ $skillOption }}" @selected($skill === $skillOption)>{{ $skillOption }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="min-h-11 rounded-md bg-slate-950 px-5 text-sm font-black text-white transition hover:bg-pink-700">Filter</button>
                <a href="{{ route('admin.training.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-200 px-4 text-sm font-black text-slate-700 transition hover:border-pink-300 hover:text-pink-700">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4">Applicant</th>
                        <th class="px-5 py-4">Skill</th>
                        <th class="px-5 py-4">Location</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Submitted</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($applications as $application)
                        <tr class="table-row-hover">
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ $application->fullName() }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ $application->email }} · {{ $application->phone_whatsapp }}</p>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-700">{{ $application->desired_skill }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $application->city_state }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-black uppercase {{ $badgeClass($application->status) }}">{{ $application->statusLabel() }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $application->created_at?->format('M j, Y g:i A') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.training.show', $application) }}" class="font-black text-pink-700 hover:text-pink-800">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm font-semibold text-slate-500">No training applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $applications->links() }}
    </section>
@endsection
