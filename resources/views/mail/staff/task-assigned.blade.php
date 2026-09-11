@extends('mail.layouts.base')

@section('title', 'Task Assigned')
@section('headerTitle', 'A New Task Assigned to you')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Hello {{ $recipient->displayName() }},</p>
    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">{{ $assigner->displayName() }} assigned a new task to you. Please review and complete it and report your progress to your HOD via email or in your staff dashboard.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:16px 0;">
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Task</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $todo->task }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Due Date</td>
            <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $todo->due_date?->format('F j, Y') }}</td>
        </tr>
        @if ($todo->order)
            <tr>
                <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Order</td>
                <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px; background-color: #ff0000; color: #ffffff;">{{ $todo->order->job_order_number ?? $todo->order->displayNumber() }}</td>
            </tr>
        @endif
        @if ($todo->notes)
            <tr>
                <td style="padding:10px;border:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:700;">Notes</td>
                <td style="padding:10px;border:1px solid #e2e8f0;font-size:13px;">{{ $todo->notes }}</td>
            </tr>
        @endif
    </table>
    {!! $outroHtml ?? '' !!}
@endsection
