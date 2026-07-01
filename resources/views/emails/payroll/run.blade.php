<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Payroll Summary</title>
<style>body{font-family:Arial,sans-serif;background:#f1f5f9;margin:0;padding:30px 0}.container{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#0f172a,#1e293b);padding:32px;text-align:center}.header h1{color:#fff;font-size:20px;margin:0}.header p{color:#94a3b8;font-size:13px;margin:6px 0 0}.body{padding:32px}.row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:14px}.row .label{color:#64748b}.row .value{font-weight:600;color:#0f172a}.total-row{display:flex;justify-content:space-between;padding:13px 0;font-size:16px;font-weight:700;border-top:2px solid #0f172a;margin-top:4px}.footer{padding:20px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;text-align:center}</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Printbuka Payroll Summary</h1>
        <p>{{ $run->periodLabel() }}</p>
    </div>
    <div class="body">
        <p style="font-size:14px;color:#475569;margin-bottom:24px">
            Hello,<br><br>
            Please find attached the payroll summary for <strong>{{ $run->periodLabel() }}</strong>, sent by <strong>{{ $sentByName }}</strong>.
        </p>

        @php
            $entries         = $run->entries;
            $totalGross      = $entries->sum('gross_salary');
            $totalDeductions = $entries->sum('total_deductions');
            $totalNet        = $entries->sum('net_salary');
        @endphp

        <div class="row"><span class="label">Payroll Period</span><span class="value">{{ $run->periodLabel() }}</span></div>
        <div class="row"><span class="label">Status</span><span class="value">{{ ucfirst($run->status) }}</span></div>
        <div class="row"><span class="label">Total Staff</span><span class="value">{{ $entries->count() }}</span></div>
        <div class="row"><span class="label">Total Gross</span><span class="value">₦{{ number_format($totalGross, 2) }}</span></div>
        <div class="row"><span class="label">Total Deductions</span><span class="value" style="color:#ef4444">-₦{{ number_format($totalDeductions, 2) }}</span></div>
        <div class="total-row"><span>Total Net Payroll</span><span style="color:#059669">₦{{ number_format($totalNet, 2) }}</span></div>

        @if ($run->payment_date)
        <p style="font-size:13px;color:#64748b;margin-top:16px">Payment Date: <strong>{{ $run->payment_date->format('M j, Y') }}</strong></p>
        @endif

        <p style="font-size:12px;color:#94a3b8;text-align:center;margin-top:24px">The full payroll report PDF is attached to this email.</p>
    </div>
    <div class="footer">&copy; {{ date('Y') }} Printbuka. This report is strictly confidential.</div>
</div>
</body>
</html>
