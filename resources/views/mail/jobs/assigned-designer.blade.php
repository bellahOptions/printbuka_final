<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Job Assignment</title>
</head>
<body style="font-family:Arial,sans-serif;color:#0f172a;line-height:1.6;">
    <p>Hello {{ $designer->displayName() }},</p>
    <p>A new job has been assigned to you.</p>
    <p><strong>Job:</strong> {{ $order->job_order_number ?? $order->displayNumber() }}</p>
    <p><strong>Client:</strong> {{ $order->customer_name }}</p>
    <p><strong>Product:</strong> {{ $order->product?->name ?? ($order->job_type ?? 'Custom order') }}</p>
    <p><strong>Status:</strong> {{ $order->status }}</p>
    <p>Please review and begin work as soon as possible.</p>
</body>
</html>
