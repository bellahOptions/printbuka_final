@extends('layouts.admin')
@section('title', 'Compose Memo | Printbuka')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    <div>
        <a href="{{ route('admin.memos.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Memos</a>
        <h1 class="text-2xl font-black text-slate-950 mt-2">Compose Memo</h1>
        <p class="text-sm text-slate-500 mt-1">Build the memo below, choose who receives it, then send.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-pink-200 bg-pink-50 p-4 text-sm font-bold text-pink-800">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.memos.store') }}" onsubmit="return confirm('Send this memo now? This cannot be undone.');" class="space-y-6">
        @csrf

        <div class="pb-card p-5">
            <label class="pb-label">Subject</label>
            <input type="text" name="subject" class="pb-input" value="{{ old('subject') }}" required maxlength="255">
        </div>

        <div class="pb-card p-5">
            <p class="text-sm font-black text-slate-900 mb-4">Memo content</p>
            @include('admin.email-builder._canvas', [
                'fieldName' => 'blocks',
                'blocks' => [],
                'variables' => ['staff_name', 'company_name'],
            ])
        </div>

        <div class="pb-card p-5" x-data="{ recipientType: '{{ old('recipient_type', 'all') }}' }">
            <p class="text-sm font-black text-slate-900 mb-4">Recipients</p>

            <div class="flex flex-wrap gap-4 mb-4">
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="radio" name="recipient_type" value="all" x-model="recipientType"> All staff
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="radio" name="recipient_type" value="role" x-model="recipientType"> By role
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="radio" name="recipient_type" value="individual" x-model="recipientType"> Specific staff
                </label>
            </div>

            <div x-show="recipientType === 'role'" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="recipient_roles[]" value="{{ $role }}">
                        {{ config('printbuka_admin.role_labels.'.$role, ucwords(str_replace('_', ' ', $role))) }}
                    </label>
                @endforeach
            </div>

            <div x-show="recipientType === 'individual'" class="max-h-64 overflow-y-auto space-y-1 border border-slate-200 rounded-xl p-3">
                @foreach ($staffList as $staff)
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="recipient_user_ids[]" value="{{ $staff->id }}">
                        {{ trim($staff->first_name.' '.$staff->last_name) }}
                        <span class="text-xs text-slate-400">({{ config('printbuka_admin.role_labels.'.$staff->role, $staff->role) }})</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="pb-btn pb-btn-primary">Send Memo</button>
            <a href="{{ route('admin.memos.index') }}" class="pb-btn pb-btn-outline">Cancel</a>
        </div>
    </form>

</div>
@endsection
