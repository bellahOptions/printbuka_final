<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Job Phase Alert</title>
</head>
<body style="font-family:Arial,sans-serif;color:#0f172a;line-height:1.6;">
    <p>Hello {{ $recipient->displayName() }},</p>
    <p>A job has moved into your team phase.</p>
    <p><strong>Job:</strong> {{ $order->job_order_number ?? $order->displayNumber() }}</p>
    <p><strong>Client:</strong> {{ $order->customer_name }}</p>
    <p><strong>Phase:</strong> {{ $phase['phase'] ?? 'Workflow Update' }}</p>
    <p><strong>Status Changed:</strong> {{ $oldStatus }} → {{ $newStatus }}</p>
    <p><strong>Responsible:</strong> {{ $phase['responsible'] ?? 'Assigned team' }}</p>
    <p>Please log into admin and process the job.</p>
</body>
</html>
