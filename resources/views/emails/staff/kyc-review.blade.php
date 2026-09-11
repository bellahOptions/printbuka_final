@extends('mail.layouts.base')

@section('title', 'KYC Review')
@section('headerTitle', $status === 'approved' ? 'KYC Approved' : 'Correction Required')
@section('headerSubtitle', $status === 'approved' ? 'Your bio-data has been verified and approved' : 'Your bio-data needs to be updated')
@section('footerNote', 'This message was sent automatically.')

@section('content')
    <style>
        .notes-box{background:#fff7ed;border-left:4px solid #f59e0b;padding:14px 16px;border-radius:0 8px 8px 0;margin-top:16px}
        .notes-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#92400e;margin-bottom:6px}
        .notes-text{font-size:14px;color:#451a03;line-height:1.6}
    </style>
    {!! $introHtml ?? '' !!}
    <p style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">Hello {{ $staff->displayName() }},</p>

    @if ($status === 'approved')
        <p style="font-size:14px;color:#475569;line-height:1.7;margin-top:12px">
            Your staff KYC bio-data form has been reviewed and <strong style="color:#059669">approved</strong>
            by <strong>{{ $reviewerName }}</strong>. No further action is required from you at this time.
        </p>
        <p style="font-size:14px;color:#475569;margin-top:12px">
            Your profile is now marked as complete. Thank you for keeping your records up to date.
        </p>
    @else
        <p style="font-size:14px;color:#475569;line-height:1.7;margin-top:12px">
            Your staff KYC bio-data form has been reviewed by <strong>{{ $reviewerName }}</strong>
            and requires <strong style="color:#d97706">corrections</strong> before it can be approved.
            Please log in to the admin portal and update the relevant sections of your bio-data.
        </p>
        @if ($notes)
            <div class="notes-box">
                <div class="notes-label">Reviewer's Notes</div>
                <div class="notes-text">{{ $notes }}</div>
            </div>
        @endif
        <p style="font-size:14px;color:#475569;margin-top:16px">
            Once you've made the necessary corrections, notify HR so they can review your profile again.
        </p>
    @endif

    <p style="font-size:13px;color:#94a3b8;margin-top:24px;text-align:center">
        If you have any questions, please contact the HR department.
    </p>
    {!! $outroHtml ?? '' !!}
@endsection
