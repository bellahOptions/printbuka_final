<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pending Jobs Reminder</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        @include('mail.partials.header', [
            'headerTitle' => 'Pending Jobs Reminder',
        ])
        <tr>
            <td style="padding:24px;line-height:1.6;">
                {!! $introHtml ?? '' !!}
                <p>Hello {{ $recipient->displayName() }},</p>
                <p>You have paid pending jobs/tasks that need attention:</p>
                <ul>
                    @foreach ($items as $item)
                        <li>
                            <strong>{{ $item['order']->job_order_number ?? $item['order']->displayNumber() }}</strong>
                            - {{ $item['order']->customer_name }}
                            - {{ $item['phase'] }}
                            - {{ $item['status'] }}
                            - {{ $item['payment_status'] }}
                            - stuck for about {{ $item['stuck_hours'] }} hour(s)
                            <br>
                            Task: {{ $item['task'] }}
                            <br>
                            <a href="{{ route('admin.orders.show', $item['order']) }}">Open job</a>
                        </li>
                    @endforeach
                </ul>
                <p>Please update these jobs in the admin dashboard.</p>
                {!! $outroHtml ?? '' !!}
            </td>
        </tr>
    </table>
</body>
</html>
