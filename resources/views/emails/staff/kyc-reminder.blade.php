@extends('mail.layouts.base')

@section('title', 'Complete Your Bio-Data Form')
@section('headerTitle', 'Printbuka')
@section('headerSubtitle', 'Staff HR Portal')
@section('footerNote', 'This is an automated notification — do not reply to this email.')

@section('content')
    <style>
        .greeting{font-size:16px;color:#0f172a;font-weight:600;margin-bottom:16px}
        .message{font-size:14px;color:#475569;line-height:1.7;margin-bottom:24px}
        .btn{display:inline-block;background:#db2777;color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px}
        .highlight{background:#fdf2f8;border-left:4px solid #db2777;padding:16px;border-radius:6px;margin:20px 0;font-size:13px;color:#831843}
    </style>
    {!! $introHtml ?? '' !!}
    <p class="greeting">Hello {{ $staff->displayName() }},</p>
    <p class="message">
        Welcome to the Printbuka team! Your account has been activated and you now have access to the staff portal.
    </p>
    <div class="highlight">
        <strong>Action Required:</strong> As part of our onboarding process, you are required to complete your
        <strong>Staff Employment Bio-Data Form</strong> within the next <strong>48 hours</strong>.
        This is a compulsory KYC requirement for all staff members.
    </div>
    <p class="message">
        The form captures your personal information, next of kin details, and banking/financial details needed for payroll processing.
        All information is kept strictly confidential.
    </p>
    <p style="text-align:center;margin:28px 0">
        <a href="{{ url('/staff/login') }}" class="btn">Complete Bio-Data Form →</a>
    </p>
    <p class="message" style="font-size:13px;color:#64748b">
        Once logged in, navigate to <strong>My Profile → Bio-Data</strong> to fill in the form.
        If you have any questions, contact HR directly.
    </p>
    {!! $outroHtml ?? '' !!}
@endsection
