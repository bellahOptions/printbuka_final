<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Printbuka Finance Report</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; margin: 0; padding: 24px; background: #f8fafc;">
    <div style="max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.08);">

        <div style="background: linear-gradient(135deg, #be185d, #9d174d); padding: 28px 32px;">
            <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #fbcfe8;">Financial Report</p>
            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff;">{{ $periodLabel }} Report</h1>
            <p style="margin: 6px 0 0; font-size: 13px; color: #fce7f3;">Generated {{ now()->format('F j, Y') }} &middot; Printbuka</p>
        </div>

        <div style="padding: 28px 32px;">
            <p style="margin: 0 0 20px; font-size: 14px; color: #374151; line-height: 1.6;">
                Please find attached the <strong>{{ $periodLabel }} Finance Report</strong> as a PDF document.
                It includes a full transaction ledger and summary KPIs for the selected period.
            </p>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                <tr>
                    <td style="padding: 12px 16px; background: #f0fdf4; border-radius: 8px 0 0 8px; border: 1px solid #bbf7d0;">
                        <p style="margin: 0; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #065f46;">Total Income</p>
                        <p style="margin: 4px 0 0; font-size: 18px; font-weight: 800; color: #047857;">&#8358;{{ number_format($incomeTotal, 2) }}</p>
                    </td>
                    <td style="width: 8px;"></td>
                    <td style="padding: 12px 16px; background: #fff1f2; border-radius: 0; border: 1px solid #fecdd3;">
                        <p style="margin: 0; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #9f1239;">Total Expenses</p>
                        <p style="margin: 4px 0 0; font-size: 18px; font-weight: 800; color: #be123c;">&#8358;{{ number_format($expenseTotal, 2) }}</p>
                    </td>
                    <td style="width: 8px;"></td>
                    <td style="padding: 12px 16px; background: #eef2ff; border-radius: 0 8px 8px 0; border: 1px solid #c7d2fe;">
                        <p style="margin: 0; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #3730a3;">Net {{ $netTotal >= 0 ? 'Profit' : 'Loss' }}</p>
                        <p style="margin: 4px 0 0; font-size: 18px; font-weight: 800; color: #1d4ed8;">&#8358;{{ number_format(abs($netTotal), 2) }}</p>
                    </td>
                </tr>
            </table>

            <p style="margin: 0 0 8px; font-size: 13px; color: #6b7280;">
                Prepared by <strong style="color: #374151;">{{ $generatedByName }}</strong>
            </p>
            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                This report is confidential and intended solely for internal use at Printbuka.
            </p>
        </div>

        <div style="padding: 16px 32px; background: #f8fafc; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0; font-size: 11px; color: #9ca3af; text-align: center;">
                &copy; {{ date('Y') }} Printbuka &middot; Your Print Partner
            </p>
        </div>
    </div>
</body>
</html>
