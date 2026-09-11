@extends('mail.layouts.base')

@section('title', $statusLabel)
@section('headerTitle', $statusLabel)

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 16px;">Hello {{ $staff->displayName() }},</p>

    @if ($status === 'terminated')
        <p style="margin:0 0 16px;">This is to notify you that your contract with Printbuka has been terminated. Your staff account access has been disabled immediately.</p>
    @elseif ($status === 'suspended')
        <p style="margin:0 0 16px;">This is to notify you that your Printbuka staff account has been suspended indefinitely. Your access has been disabled until management restores it.</p>
    @else
        <p style="margin:0 0 16px;">Your Printbuka staff account has been activated for onboarding. You can now sign in with your verified email address.</p>
    @endif

    @if (filled($reason))
        <p style="margin:0 0 16px;"><strong>Note:</strong> {{ $reason }}</p>
    @endif

    <p style="margin:0;">Regards,<br>Printbuka Management</p>
    {!! $outroHtml ?? '' !!}
@endsection
