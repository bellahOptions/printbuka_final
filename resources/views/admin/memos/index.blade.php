@extends('layouts.admin')
@section('title', 'Internal Memos | Printbuka')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div class="pb-page-header">
        <div>
            <h1 class="pb-page-title">Internal Memos</h1>
            <p class="pb-page-subtitle">Compose and send memos to staff, and review what's already gone out.</p>
        </div>
        <a href="{{ route('admin.memos.create') }}" class="pb-btn pb-btn-primary">+ New Memo</a>
    </div>

    @if (session('status'))
        <div class="pb-alert pb-alert-success">{{ session('status') }}</div>
    @endif

    <livewire:admin.internal-memos-table />

</div>
@endsection
