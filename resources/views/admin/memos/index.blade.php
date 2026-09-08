@extends('layouts.admin')
@section('title', 'Internal Memos | Printbuka')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-950">Internal Memos</h1>
            <p class="text-sm text-slate-500 mt-1">Compose and send memos to staff, and review what's already gone out.</p>
        </div>
        <a href="{{ route('admin.memos.create') }}" class="pb-btn pb-btn-primary">+ New Memo</a>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <livewire:admin.internal-memos-table />
    </div>

</div>
@endsection
