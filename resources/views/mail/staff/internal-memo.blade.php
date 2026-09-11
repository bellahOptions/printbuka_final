@extends('mail.layouts.base')

@section('title', $memo->subject)
@section('headerBadge', 'INTERNAL MEMO')
@section('footerNote', 'This is an internal memo sent to '.$recipient->displayName().' — please do not forward outside the organization.')

@section('content')
    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#db2777;text-transform:uppercase;letter-spacing:0.06em;">{{ $memo->subject }}</p>
    <p style="margin:0 0 20px;font-size:12px;color:#94a3b8;">Sent {{ $memo->sent_at?->format('F j, Y g:i A') ?? now()->format('F j, Y g:i A') }} by {{ $memo->sentBy?->displayName() ?? 'Printbuka Admin' }}</p>
    {!! $bodyHtml !!}
@endsection
