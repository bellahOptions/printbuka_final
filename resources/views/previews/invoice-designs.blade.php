<!DOCTYPE html>
<html lang="en" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Invoice Design Previews</title>
        <style>
            :root {
                color-scheme: light;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                padding: 24px;
                font-family: Arial, sans-serif;
                background: #f1f5f9;
                color: #0f172a;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
            }

            h1 {
                margin: 0 0 8px;
                font-size: 28px;
            }

            p {
                margin: 0 0 18px;
                color: #334155;
            }

            .links {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 20px;
            }

            .links a {
                text-decoration: none;
                padding: 10px 14px;
                border-radius: 10px;
                background: #0f172a;
                color: #ffffff;
                font-size: 14px;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 16px;
            }

            .panel {
                background: #ffffff;
                border: 1px solid #dbeafe;
                border-radius: 12px;
                overflow: hidden;
            }

            .panel h2 {
                margin: 0;
                padding: 12px 14px;
                background: #e2e8f0;
                font-size: 14px;
                color: #0f172a;
            }

            .panel iframe {
                width: 100%;
                height: 720px;
                border: 0;
                background: #ffffff;
            }

            @media (max-width: 980px) {
                .grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Invoice and Email Preview</h1>
            <p>Open these previews locally to validate invoice, receipt, and email designs.</p>

            <div class="links">
                <a href="{{ route('local-previews.invoices.pdf') }}" target="_blank" rel="noopener">Open Invoice PDF Preview</a>
                <a href="{{ route('local-previews.invoices.receipt-pdf') }}" target="_blank" rel="noopener">Open Receipt PDF Preview</a>
                <a href="{{ route('local-previews.invoices.email') }}" target="_blank" rel="noopener">Open Invoice Email Preview</a>
                <a href="{{ route('local-previews.invoices.paid-receipt-email') }}" target="_blank" rel="noopener">Open Paid Receipt Email Preview</a>
            </div>

            <div class="grid">
                <section class="panel">
                    <h2>Invoice PDF</h2>
                    <iframe src="{{ route('local-previews.invoices.pdf') }}" loading="lazy"></iframe>
                </section>

                <section class="panel">
                    <h2>Invoice Email</h2>
                    <iframe src="{{ route('local-previews.invoices.email') }}" loading="lazy"></iframe>
                </section>

                <section class="panel">
                    <h2>Receipt PDF</h2>
                    <iframe src="{{ route('local-previews.invoices.receipt-pdf') }}" loading="lazy"></iframe>
                </section>

                <section class="panel">
                    <h2>Paid Receipt Email</h2>
                    <iframe src="{{ route('local-previews.invoices.paid-receipt-email') }}" loading="lazy"></iframe>
                </section>
            </div>
        </div>
    </body>
</html>
