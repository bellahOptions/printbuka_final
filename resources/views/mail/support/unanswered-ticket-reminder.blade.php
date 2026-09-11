@extends('mail.layouts.base')

@section('title', 'Unanswered Ticket Reminder')
@section('headerBadge', 'SUPPORT REMINDER')
@section('headerTitle', 'Unanswered Support Tickets')
@section('headerSubtitle', $tickets->count().' ticket(s) have been awaiting response for at least '.$thresholdHours.' hour(s).')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 14px;font-size:14px;line-height:1.6;">Hello {{ $recipient->displayName() }},</p>
    <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">Please review the unanswered support queue below and respond as soon as possible.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <th align="left" style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;">Ticket</th>
            <th align="left" style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;">Client</th>
            <th align="left" style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;">Priority</th>
            <th align="left" style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;">Waiting</th>
        </tr>
        @foreach ($tickets as $ticket)
            <tr>
                <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;font-weight:700;">
                    {{ $ticket->ticket_number }}<br>
                    <span style="font-weight:600;color:#475569;">{{ Str::limit((string) $ticket->subject, 45) }}</span>
                </td>
                <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $ticket->user?->displayName() ?? 'Unknown user' }}</td>
                <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ strtoupper((string) $ticket->priority) }}</td>
                <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ max(1, now()->diffInHours($ticket->updated_at)) }}h</td>
            </tr>
        @endforeach
    </table>

    <p style="margin:16px 0 0;font-size:13px;line-height:1.6;color:#475569;">Open the admin support portal to respond and keep SLA compliance on track.</p>
    <a href="{{ route('admin.support.index') }}" style="display:inline-block;margin-top:14px;background:#EC268F;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 18px;border-radius:8px;">Open Support Queue</a>
    {!! $outroHtml ?? '' !!}
@endsection
