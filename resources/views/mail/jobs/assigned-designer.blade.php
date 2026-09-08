<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Job Assignment</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        @include('mail.partials.header', [
            'headerTitle' => 'Job Assignment',
        ])
        <tr>
            <td style="padding:24px;line-height:1.6;">
                {!! $introHtml ?? '' !!}
                <p>Hello {{ $designer->displayName() }},</p>
                <p>A new job has been assigned to you.</p>
                <p><strong>Job:</strong> {{ $order->job_order_number ?? $order->displayNumber() }}</p>
                <p><strong>Client:</strong> {{ $order->customer_name }}</p>
                <p><strong>Product:</strong> {{ $order->product?->name ?? ($order->job_type ?? 'Custom order') }}</p>
                <p><strong>Status:</strong> {{ $order->status }}</p>
                <p>Please review and begin work as soon as possible.</p>
                {!! $outroHtml ?? '' !!}
            </td>
        </tr>
    </table>
</body>
</html>
