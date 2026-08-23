<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Preview — {{ $entry['name'] }}</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;padding:24px;">
    <p style="font-size:12px;color:#94a3b8;margin:0 0 12px;">Preview with sample placeholder values — the intro and footer only. The real PDF's figures, tables, and line items are not shown here.</p>
    <div style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;">
        {!! $introHtml !!}
        <p style="color:#94a3b8;font-size:12px;margin:16px 0;border-top:1px dashed #e2e8f0;padding-top:16px;">— PDF's main content (header, line items, totals) renders here —</p>
        {!! $outroHtml !!}
    </div>
</body>
</html>
