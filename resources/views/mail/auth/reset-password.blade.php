@extends('mail.layouts.base')

@php
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));
@endphp

@section('title', 'Reset your password')
@section('headerBadge', 'PASSWORD RESET')
@section('headerTitle', 'Reset your password')
@section('headerSubtitle', 'We received a request to reset the password for your account.')
@section('footerNote', 'For your security, this link can only be used once.')

@section('content')
    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">Hello {{ $user->first_name ?? $user->displayName() }},</p>
    <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#475569;">
        Someone recently requested a password reset for your {{ $siteName }} account.
        If this was you, click the button below to set a new password.
    </p>

    {{-- CTA Button --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td align="center">
                <a href="{{ $resetUrl }}"
                   style="display:inline-block;padding:14px 36px;background:#EC268F;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:700;font-size:14px;letter-spacing:.3px;">
                    Reset Password &rarr;
                </a>
            </td>
        </tr>
    </table>

    {{-- Expiry + security note --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;">
                <p style="margin:0 0 8px;font-size:13px;color:#374151;line-height:1.6;">
                    <strong>Link expires in {{ $expiryMinutes }} minutes.</strong>
                    After that you'll need to submit a new request.
                </p>
                <p style="margin:0;font-size:13px;color:#64748b;line-height:1.6;">
                    If you didn't request a password reset, you can safely ignore this email.
                    Your password will not be changed.
                </p>
            </td>
        </tr>
    </table>

    {{-- Fallback URL --}}
    <p style="margin:0 0 6px;font-size:12px;color:#94a3b8;line-height:1.6;">
        If the button above doesn't work, copy and paste this URL into your browser:
    </p>
    <p style="margin:0;font-size:11px;color:#64748b;word-break:break-all;line-height:1.7;">
        {{ $resetUrl }}
    </p>
@endsection
