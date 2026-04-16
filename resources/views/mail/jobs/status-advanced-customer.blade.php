<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Status Update</title>
</head>
<body style="font-family:Arial,sans-serif;color:#0f172a;line-height:1.6;">
    <p>Hello {{ $order->customer_name }},</p>
    <p>Your order has advanced to a new stage.</p>
    <p><strong>Order:</strong> {{ $order->job_order_number ?? $order->displayNumber() }}</p>
    <p><strong>Previous Status:</strong> {{ $oldStatus }}</p>
    <p><strong>Current Status:</strong> {{ $newStatus }}</p>
    <p>We will keep notifying you as your job progresses.</p>
</body>
</html>
