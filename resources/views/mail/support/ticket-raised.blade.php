@extends('mail.layouts.base')

@php
    $ticketUrl = route('admin.support.show', $ticket);
@endphp

@section('title', 'New Support Ticket')
@section('headerBadge', 'SUPPORT ALERT')
@section('headerTitle', 'New Support Ticket Raised')
@section('headerSubtitle', 'Ticket '.$ticket->ticket_number.' needs attention from the admin team.')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 14px;font-size:14px;line-height:1.6;">Hello {{ $recipient->displayName() }},</p>
    <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">A new support ticket has been submitted. Please review and assign action quickly.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 18px;">
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Ticket Number</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;font-weight:700;">{{ $ticket->ticket_number }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Raised By</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $ticket->user?->displayName() ?? 'Unknown user' }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Category</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ ucfirst((string) $ticket->category) }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Priority</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ strtoupper((string) $ticket->priority) }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Subject</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $ticket->subject }}</td>
        </tr>
    </table>

    <div style="margin:0 0 18px;padding:14px;border:1px solid #fbcfe8;background:#fdf2f8;border-radius:10px;">
        <p style="margin:0;font-size:13px;line-height:1.6;color:#7f1d1d;">{{ Str::limit((string) $ticket->message, 350) }}</p>
    </div>

    <a href="{{ $ticketUrl }}" style="display:inline-block;background:#EC268F;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 18px;border-radius:8px;">Open Ticket</a>
    {!! $outroHtml ?? '' !!}
@endsection
