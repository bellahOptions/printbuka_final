@extends('mail.layouts.base')

@section('title', $subject ?? 'Message from Printbuka Admin')
@section('headerBadge', 'DIRECT ADMIN MESSAGE')
@section('headerTitle', 'Hello '.$recipientName)

@section('content')
    <p style="margin:0 0 14px 0;font-size:15px;line-height:1.7;color:#334155;">You received a direct message from Printbuka administration.</p>
    <div style="margin:0 0 18px 0;padding:16px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;">
        {!! nl2br(e($messageBody)) !!}
    </div>
    <p style="margin:0;font-size:14px;line-height:1.7;color:#475569;">
        Sender: <strong>{{ $senderName }}</strong>
    </p>
    <p style="margin:6px 0 0 0;font-size:13px;line-height:1.7;color:#64748b;">
        Reply directly to this email to reach the sender.
    </p>
@endsection
