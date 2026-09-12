@extends('mail.layouts.base')

@section('title', 'Staff Query Notice')
@section('headerTitle', 'Official Query Notice')
@section('headerSubtitle', 'Printbuka HR Department')
@section('footerNote', 'Strictly Confidential — HR Use Only.')

@section('content')
    <style>
        .label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-bottom:4px}
        .value{font-size:14px;color:#0f172a;font-weight:600;margin-bottom:16px}
        .divider{border:none;border-top:1px solid #e2e8f0;margin:20px 0}
        .description{background:#fef2f2;border-left:4px solid #ef4444;padding:16px;border-radius:6px;font-size:14px;color:#7f1d1d;line-height:1.6;margin:20px 0}
        .btn{display:inline-block;background:#7f1d1d;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px}
    </style>
    {!! $introHtml ?? '' !!}
    <p style="font-size:15px;color:#0f172a;font-weight:600">Dear {{ $query->staff?->displayName() }},</p>
    <p style="font-size:14px;color:#475569;line-height:1.7">
        A formal query has been issued against you by the HR Department. Please review the details below and respond through the staff portal within the stipulated timeframe.
    </p>
    <hr class="divider">
    <div class="label">Query Reference</div><div class="value">{{ $query->query_number }}</div>
    <div class="label">Query Type</div><div class="value">{{ $query->typeLabel() }}</div>
    <div class="label">Date Issued</div><div class="value">{{ $query->query_date->format('F j, Y') }}</div>
    <div class="label">Subject</div><div class="value">{{ $query->subject }}</div>
    @if ($query->response_due_date)
    <div class="label">Response Due</div><div class="value" style="color:#dc2626">{{ $query->response_due_date->format('F j, Y') }}</div>
    @endif
    <div class="label">Description</div>
    <div class="description">{!! $query->description !!}</div>
    <p style="text-align:center;margin-top:28px">
        <a href="{{ url('/staff/login') }}" class="btn">Respond to Query →</a>
    </p>
    <p style="font-size:12px;color:#64748b;margin-top:20px;line-height:1.6">
        Please log in to the staff portal and navigate to <strong>My Queries</strong> to submit your formal response.
        Failure to respond by the due date may result in further disciplinary action.
    </p>
    {!! $outroHtml ?? '' !!}
@endsection
