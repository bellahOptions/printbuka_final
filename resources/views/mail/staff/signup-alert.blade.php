@extends('mail.layouts.base')

@section('title', 'New Staff Signup')
@section('headerBadge', 'PRINTBUKA STAFF GOVERNANCE')
@section('headerTitle', 'New staff signup awaiting approval')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Hello {{ $recipient->displayName() }},</p>
    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">A new staff registration was submitted and requires Process & Technology Manager review.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:16px 0;">
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Name</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $staff->displayName() }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Email</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $staff->email }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Phone</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $staff->phone }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Submitted</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $staff->created_at?->format('F j, Y g:i A') }}</td>
        </tr>
    </table>

    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Please sign in to the admin dashboard and review the pending staff account.</p>
    {!! $outroHtml ?? '' !!}
@endsection
