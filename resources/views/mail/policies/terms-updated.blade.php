@extends('mail.layouts.base')

@section('title', 'Terms & Conditions Updated')
@section('headerTitle', 'Terms & Conditions Updated')
@section('headerSubtitle', 'Please review the latest terms for using Printbuka services.')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 16px;">Hello {{ $customer->displayName() }},</p>
    <p style="margin:0 0 16px;line-height:1.6;">
        We have updated our Terms & Conditions. Please review the latest version to stay informed on current order and service rules.
    </p>
    <p style="margin:0 0 20px;line-height:1.6;">
        <strong>Updated on:</strong>
        {{ optional($terms->updated_at)->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A') }}
    </p>
    <a href="{{ $termsUrl }}" style="display:inline-block;background:#EC268F;color:#ffffff;padding:12px 18px;border-radius:6px;font-weight:700;text-decoration:none;">
        Review Terms & Conditions
    </a>
    {!! $outroHtml ?? '' !!}
@endsection
