@extends('mail.layouts.base')

@section('title', 'Staff Query Closed')
@section('headerTitle', 'Query Closed — Conversation Log')
@section('headerSubtitle', 'Printbuka HR Department')
@section('footerNote', 'Strictly Confidential — HR Use Only.')

@section('content')
    <style>
        .label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-bottom:4px}
        .value{font-size:14px;color:#0f172a;font-weight:600;margin-bottom:16px}
        .divider{border:none;border-top:1px solid #e2e8f0;margin:20px 0}
        .resolution{background:#ecfdf5;border-left:4px solid #10b981;padding:16px;border-radius:6px;font-size:14px;color:#064e3b;line-height:1.6;margin:20px 0}
        .message{border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;margin-bottom:12px}
        .message-staff{background:#f8fafc}
        .message-hr{background:#fdf2f8}
        .message-meta{font-size:11px;color:#94a3b8;margin-bottom:6px}
        .message-author{font-weight:700;color:#0f172a}
        .message-body{font-size:13px;color:#334155;line-height:1.6;white-space:pre-line}
    </style>
    {!! $introHtml ?? '' !!}
    <p style="font-size:15px;color:#0f172a;font-weight:600">This query has been closed.</p>
    <p style="font-size:14px;color:#475569;line-height:1.7">
        Below is the complete conversation log for this staff query, provided for your records.
    </p>
    <hr class="divider">
    <div class="label">Query Reference</div><div class="value">{{ $query->query_number }}</div>
    <div class="label">Query Type</div><div class="value">{{ $query->typeLabel() }}</div>
    <div class="label">Staff Member</div><div class="value">{{ $query->staff?->displayName() }}</div>
    <div class="label">Issued By</div><div class="value">{{ $query->issuedBy?->displayName() }}</div>
    <div class="label">Subject</div><div class="value">{{ $query->subject }}</div>
    <div class="label">Closed On</div><div class="value">{{ $query->resolved_at?->format('F j, Y g:i A') }}</div>

    <hr class="divider">
    <p style="font-size:13px;font-weight:700;color:#0f172a;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">Conversation</p>

    @forelse ($thread as $item)
        <div class="message {{ $item['is_staff'] ? 'message-staff' : 'message-hr' }}">
            <div class="message-meta">
                <span class="message-author">{{ $item['author']?->displayName() }}</span>
                — {{ $item['at']?->format('M j, Y g:i A') }}
            </div>
            <div class="message-body">{!! $item['is_html'] ? $item['body'] : e($item['body']) !!}</div>
        </div>
    @empty
        <p style="font-size:13px;color:#94a3b8">No responses or comments were recorded on this query.</p>
    @endforelse

    @if ($query->resolution_notes)
        <div class="label" style="margin-top:20px">Resolution Notes</div>
        <div class="resolution">{!! $query->resolution_notes !!}</div>
    @endif

    {!! $outroHtml ?? '' !!}
@endsection
