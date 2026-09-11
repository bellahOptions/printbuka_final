@extends('mail.layouts.base')

@php
    $siteName = trim((string) (\App\Support\SiteSettings::all()['site_name'] ?? 'Printbuka'));
    $state = $enabled ? 'turned ON' : 'turned OFF';
@endphp

@section('title', 'OTP verification setting changed')
@section('headerBadge', 'SECURITY SETTING')
@section('headerTitle', 'Email OTP verification '.$state)
@section('headerSubtitle', 'This affects how staff without an authenticator app sign in.')

@section('content')
    <p style="margin:0 0 16px;font-size:14px;line-height:1.7;">Hello {{ $recipient->first_name ?? $recipient->displayName() }},</p>

    @if($isActingAdmin)
        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
            You have {{ $state === 'turned ON' ? 'turned on' : 'turned off' }} email OTP verification for staff and admin logins on {{ $siteName }}.
        </p>
    @else
        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
            {{ $actor->displayName() }} has {{ $state === 'turned ON' ? 'turned on' : 'turned off' }} email OTP verification for staff and admin logins on {{ $siteName }}.
        </p>
    @endif

    @if($enabled)
        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
            Staff who have not set up an authenticator app will now be sent a one-time code by email each time they sign in. Staff who already use an authenticator app are unaffected.
        </p>
    @else
        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#475569;">
            Staff who have not set up an authenticator app will now be required to set one up on their next login, as before this feature existed. Staff who already use an authenticator app are unaffected.
        </p>
    @endif

    <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
        If you did not expect this change, contact a super admin immediately.
    </p>
@endsection
