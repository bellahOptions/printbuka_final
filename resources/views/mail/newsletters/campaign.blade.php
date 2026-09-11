@extends('mail.layouts.base')

@section('title', $campaign->subject)
@section('headerBadge', 'NEWSLETTER')
@section('footerNote', 'You are receiving this because you registered on Printbuka.')

@if (filled($campaign->preheader))
    @section('preheader', $campaign->preheader)
@endif

@section('content')
    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#db2777;text-transform:uppercase;letter-spacing:0.06em;">{{ $campaign->subject }}</p>
    <p style="margin:0 0 20px;font-size:12px;color:#94a3b8;">Sent {{ $campaign->sent_at?->format('F j, Y g:i A') ?? now()->format('F j, Y g:i A') }} by Printbuka</p>
    {!! $bodyHtml !!}
@endsection
