@extends('layouts.admin')
@section('title', $memo->subject.' | Memos | Printbuka')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div>
        <a href="{{ route('admin.memos.index') }}" class="text-sm font-black text-pink-600 hover:text-pink-800">← Back to Memos</a>
        <h1 class="text-2xl font-black text-slate-950 mt-2">{{ $memo->subject }}</h1>
        <p class="text-sm text-slate-500 mt-1">
            Sent {{ $memo->sent_at?->format('F j, Y g:i A') ?? 'not yet' }} by {{ $memo->sentBy?->displayName() }}
            — {{ $memo->emails_sent }} delivered{{ $memo->emails_failed > 0 ? ', '.$memo->emails_failed.' failed' : '' }} of {{ $memo->recipient_count }} recipient(s)
        </p>
    </div>

    <div class="pb-card p-6">
        {!! \App\Support\EmailBlockRenderer::render($memo->blocks ?? [], ['staff_name' => '[Staff Name]', 'company_name' => config('app.name', 'Printbuka')]) !!}
    </div>

</div>
@endsection
