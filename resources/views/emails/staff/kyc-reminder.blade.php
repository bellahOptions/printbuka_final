<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Complete Your Bio-Data Form</title>
<style>body{font-family:Arial,sans-serif;background:#f1f5f9;margin:0;padding:30px 0}.container{max-width:580px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}.header{background:linear-gradient(135deg,#0f172a,#1e293b);padding:36px 32px;text-align:center}.header h1{color:#fff;font-size:22px;margin:0}.header p{color:#94a3b8;font-size:13px;margin:8px 0 0}.body{padding:32px}.greeting{font-size:16px;color:#0f172a;font-weight:600;margin-bottom:16px}.message{font-size:14px;color:#475569;line-height:1.7;margin-bottom:24px}.btn{display:inline-block;background:#db2777;color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px}.highlight{background:#fdf2f8;border-left:4px solid #db2777;padding:16px;border-radius:6px;margin:20px 0;font-size:13px;color:#831843}.footer{padding:20px 32px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;text-align:center}</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Printbuka</h1>
        <p>Staff HR Portal</p>
    </div>
    <div class="body">
        <p class="greeting">Hello {{ $staff->displayName() }},</p>
        <p class="message">
            Welcome to the Printbuka team! Your account has been activated and you now have access to the staff portal.
        </p>
        <div class="highlight">
            <strong>Action Required:</strong> As part of our onboarding process, you are required to complete your
            <strong>Staff Employment Bio-Data Form</strong> within the next <strong>48 hours</strong>.
            This is a compulsory KYC requirement for all staff members.
        </div>
        <p class="message">
            The form captures your personal information, next of kin details, and banking/financial details needed for payroll processing.
            All information is kept strictly confidential.
        </p>
        <p style="text-align:center;margin:28px 0">
            <a href="{{ url('/staff/login') }}" class="btn">Complete Bio-Data Form →</a>
        </p>
        <p class="message" style="font-size:13px;color:#64748b">
            Once logged in, navigate to <strong>My Profile → Bio-Data</strong> to fill in the form.
            If you have any questions, contact HR directly.
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Printbuka. This is an automated notification — do not reply to this email.
    </div>
</div>
</body>
</html>
