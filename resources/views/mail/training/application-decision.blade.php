@extends('mail.layouts.training')

@section('title', 'PGTP Application Update')

@php
    $accepted = $application->status === \App\Models\Training::STATUS_ACCEPTED;
    $applicantName = $application->fullName();
@endphp

@section('content')
    <h1 style="margin:18px 0 0;font-size:28px;line-height:1.2;">
        {{ $accepted ? 'Congratulations, you have been accepted.' : 'Thank you for applying.' }}
    </h1>
    <p style="margin:10px 0 0;color:#cbd5e1;font-size:14px;line-height:1.7;">Printbuka Graduate Trainee Program application update.</p>

    <p style="margin:18px 0 14px;font-size:15px;line-height:1.7;">Hello {{ $applicantName }},</p>

    @if ($accepted)
        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">We are pleased to let you know that your application for the Printbuka Graduate Trainee Program has been accepted.</p>
        <div style="margin:0 0 20px;padding:16px;border:1px solid #bae6fd;background:#f0f9ff;border-radius:10px;">
            <p style="margin:0 0 6px;font-size:12px;font-weight:700;color:#0369a1;text-transform:uppercase;letter-spacing:0.06em;">Next step</p>
            <p style="margin:0;font-size:13px;line-height:1.7;color:#0c4a6e;">Our team will contact you with onboarding details, screening instructions, and the training schedule.</p>
        </div>
    @else
        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">After reviewing your application, we are unable to offer you a place in this PGTP cohort.</p>
        <div style="margin:0 0 20px;padding:16px;border:1px solid #fbcfe8;background:#fdf2f8;border-radius:10px;">
            <p style="margin:0 0 6px;font-size:12px;font-weight:700;color:#be185d;text-transform:uppercase;letter-spacing:0.06em;">Keep building</p>
            <p style="margin:0;font-size:13px;line-height:1.7;color:#831843;">We appreciate your interest and encourage you to keep learning, practising, and watching out for future Printbuka opportunities.</p>
        </div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:20px 0;">
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Selected track</td>
            <td style="padding:12px;border:1px solid #e2e8f0;font-size:13px;">{{ $application->desired_skill }}</td>
        </tr>
        <tr>
            <td style="padding:12px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;">Decision</td>
            <td style="padding:12px;border:1px solid #e2e8f0;font-size:13px;">{{ $application->statusLabel() }}</td>
        </tr>
    </table>

    @if (filled($application->decision_note))
        <div style="padding:14px;border:1px solid #e2e8f0;background:#f8fafc;border-radius:10px;">
            <p style="margin:0 0 6px;font-size:12px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:0.06em;">Note from Printbuka</p>
            <p style="margin:0;font-size:13px;line-height:1.7;color:#334155;">{{ $application->decision_note }}</p>
        </div>
    @endif
@endsection
