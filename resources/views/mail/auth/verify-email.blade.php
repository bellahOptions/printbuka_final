@extends('mail.layouts.base')

@php
    $settings = \App\Support\SiteSettings::all();
    $siteName = trim((string) ($settings['site_name'] ?? 'Printbuka'));
@endphp

@section('title', 'Verify your email address')
@section('headerBadge', 'VERIFY EMAIL')
@section('headerTitle', 'Confirm your email address')
@section('headerSubtitle', 'One quick step to activate your account.')
@section('footerNote', "If you didn't create an account with ".$siteName.', you can safely ignore this email.')

@section('content')
    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">Hello {{ $user->first_name ?? $user->displayName() }},</p>
    <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#475569;">
        Thanks for signing up with {{ $siteName }}! Before you can start placing orders, we need to verify that this email address belongs to you.
        Click the button below to confirm your address.
    </p>

    {{-- CTA Button --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td align="center">
                <a href="{{ $verificationUrl }}"
                   style="display:inline-block;padding:14px 36px;background:#EC268F;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:700;font-size:14px;letter-spacing:.3px;">
                    Verify Email Address &rarr;
                </a>
            </td>
        </tr>
    </table>

    {{-- Expiry note --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td style="background:#fef9c3;border:1px solid #fde047;border-radius:8px;padding:14px 16px;">
                <p style="margin:0;font-size:13px;color:#713f12;line-height:1.6;">
                    <strong>Note:</strong> This verification link expires in <strong>60 minutes</strong>.
                    If it has expired, simply sign in to request a new one.
                </p>
            </td>
        </tr>
    </table>

    {{-- Fallback URL --}}
    <p style="margin:0 0 6px;font-size:12px;color:#94a3b8;line-height:1.6;">
        If the button above doesn't work, copy and paste this URL into your browser:
    </p>
    <p style="margin:0;font-size:11px;color:#64748b;word-break:break-all;line-height:1.7;">
        {{ $verificationUrl }}
    </p>
@endsection
