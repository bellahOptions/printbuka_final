@extends('mail.layouts.base')

@php
    $siteName = trim((string) (\App\Support\SiteSettings::all()['site_name'] ?? 'Printbuka'));
@endphp

@section('title', 'Your sign-in code')
@section('headerBadge', 'SIGN-IN CODE')
@section('headerTitle', 'Your verification code')
@section('headerSubtitle', 'Use this code to finish signing in to your staff account.')

@section('content')
    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">Hello {{ $user->first_name ?? $user->displayName() }},</p>
    <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#475569;">
        Enter the code below to verify it's you. This code was requested for your {{ $siteName }} staff account.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td align="center">
                <span style="display:inline-block;padding:16px 32px;background:#f1f5f9;border-radius:10px;font-size:32px;font-weight:700;letter-spacing:0.3em;color:#0f172a;">{{ $code }}</span>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td style="background:#fef9c3;border:1px solid #fde047;border-radius:8px;padding:14px 16px;">
                <p style="margin:0;font-size:13px;color:#713f12;line-height:1.6;">
                    <strong>Note:</strong> This code expires in <strong>{{ $expiryMinutes }} minutes</strong> and can only be used once.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
        If you didn't request this code, you can safely ignore this email — someone may have mistyped their email address while trying to sign in.
    </p>
@endsection
