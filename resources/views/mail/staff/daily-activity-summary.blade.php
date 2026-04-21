<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Daily Staff Activity Summary</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6;">
    <p>Hello {{ $recipient->displayName() }},</p>
    <p>
        Staff activity summary for <strong>{{ $reportDate->format('l, F j, Y') }}</strong>.
        Total tracked actions: <strong>{{ number_format($summary['total']) }}</strong>.
    </p>

    @if ($summary['total'] === 0)
        <p>No staff activity was recorded for this business day.</p>
    @else
        <h3 style="margin-bottom: 6px;">By Staff</h3>
        @foreach ($summary['by_staff'] as $staff)
            <div style="margin-bottom: 12px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                <p style="margin: 0 0 6px;">
                    <strong>{{ $staff['name'] }}</strong>
                    ({{ $staff['role'] }} · {{ $staff['department'] }}) -
                    {{ number_format($staff['actions']) }} action(s)
                </p>
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($staff['entries'] as $entry)
                        <li>
                            <strong>{{ $entry['time'] }}</strong> -
                            {{ $entry['action'] }}
                            ({{ $entry['route'] }}, {{ $entry['subject'] }})
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <h3 style="margin-bottom: 6px;">Top Routes</h3>
        <ul style="padding-left: 18px;">
            @foreach ($summary['by_route']->take(10) as $route)
                <li>{{ $route['route'] }} - {{ number_format($route['count']) }} action(s)</li>
            @endforeach
        </ul>
    @endif

    <p style="margin-top: 16px;">Regards,<br>Printbuka System</p>
</body>
</html>
