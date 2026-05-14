@extends('mail.layouts.training')

@section('title', 'PGTP Application Received')

@php
    $applicantName = trim($application->first_name.' '.$application->last_name);
@endphp

@section('content')
    <h1 style="margin:18px 0 0;font-size:28px;line-height:1.2;">Your application is in.</h1>
    <p style="margin:10px 0 0;color:#cbd5e1;font-size:14px;line-height:1.7;">Thanks for applying to the Printbuka Graduate Trainee Program.</p>

    <p style="margin:18px 0 14px;font-size:15px;line-height:1.7;">Hello {{ $applicantName }},</p>
    <p style="margin:0 0 18px;font-size:14px;line-height:1.7;">We have received your PGTP application. Our team will review your details and contact shortlisted applicants through the email or WhatsApp number provided.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:20px 0;">
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Selected track</td>
            <td style="padding:12px;border:1px solid #e2e8f0;font-size:13px;">{{ $application->desired_skill }}</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Phone / WhatsApp</td>
            <td style="padding:12px;border:1px solid #e2e8f0;font-size:13px;">{{ $application->phone_whatsapp }}</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Submitted</td>
            <td style="padding:12px;border:1px solid #e2e8f0;font-size:13px;">{{ $application->created_at?->format('F j, Y g:i A') }}</td>
        </tr>
    </table>

    <div style="margin:0 0 20px;padding:16px;border:1px solid #fbcfe8;background:#fdf2f8;border-radius:10px;">
        <p style="margin:0 0 6px;font-size:12px;font-weight:700;color:#be185d;text-transform:uppercase;letter-spacing:0.06em;">What happens next</p>
        <p style="margin:0;font-size:13px;line-height:1.7;color:#831843;">Keep an eye on your inbox and WhatsApp. If shortlisted, you may receive a screening message or a practical task from Printbuka.</p>
    </div>

    <p style="margin:0;font-size:14px;line-height:1.7;">Thank you for taking this step. We are rooting for serious, curious applicants who are ready to learn by doing.</p>
@endsection
