@extends('mail.layouts.base')

@section('title', 'Task Review Outcome')
@section('headerTitle', $outcome.' Notice')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Hello {{ $recipient->displayName() }},</p>

    @if ($rating === 1)
        <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Your completed task was reviewed with a <strong>1/5</strong> rating. This is a warning notice and requires immediate improvement.</p>
    @else
        <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Great work. Your completed task was reviewed with a <strong>{{ $rating }}/5</strong> rating.</p>
    @endif

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:16px 0;">
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Task</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $todo->task }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Rating</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $rating }}/5</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Reviewed By</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $todo->reviewer?->displayName() ?? 'Supervisor' }}</td>
        </tr>
        @if ($todo->review_comments)
            <tr>
                <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Manager Comment</td>
                <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $todo->review_comments }}</td>
            </tr>
        @endif
    </table>
    {!! $outroHtml ?? '' !!}
@endsection
