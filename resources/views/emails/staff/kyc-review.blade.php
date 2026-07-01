<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>KYC Review</title>
<style>body{font-family:Arial,sans-serif;background:#f1f5f9;margin:0;padding:30px 0}.container{max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}.header-approved{background:linear-gradient(135deg,#064e3b,#065f46);padding:32px;text-align:center}.header-correction{background:linear-gradient(135deg,#78350f,#92400e);padding:32px;text-align:center}.header h1{color:#fff;font-size:20px;margin:0}.header p{color:rgba(255,255,255,.75);font-size:13px;margin:8px 0 0}.body{padding:32px}.notes-box{background:#fff7ed;border-left:4px solid #f59e0b;padding:14px 16px;border-radius:0 8px 8px 0;margin-top:16px}.notes-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#92400e;margin-bottom:6px}.notes-text{font-size:14px;color:#451a03;line-height:1.6}.footer{padding:20px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;text-align:center}</style>
</head>
<body>
<div class="container">
    @if ($status === 'approved')
        <div class="header header-approved">
            <h1>KYC Approved ✓</h1>
            <p>Your bio-data has been verified and approved</p>
        </div>
    @else
        <div class="header header-correction">
            <h1>Correction Required</h1>
            <p>Your bio-data needs to be updated</p>
        </div>
    @endif

    <div class="body">
        <p style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">Hello {{ $staff->displayName() }},</p>

        @if ($status === 'approved')
            <p style="font-size:14px;color:#475569;line-height:1.7;margin-top:12px">
                Your staff KYC bio-data form has been reviewed and <strong style="color:#059669">approved</strong>
                by <strong>{{ $reviewerName }}</strong>. No further action is required from you at this time.
            </p>
            <p style="font-size:14px;color:#475569;margin-top:12px">
                Your profile is now marked as complete. Thank you for keeping your records up to date.
            </p>
        @else
            <p style="font-size:14px;color:#475569;line-height:1.7;margin-top:12px">
                Your staff KYC bio-data form has been reviewed by <strong>{{ $reviewerName }}</strong>
                and requires <strong style="color:#d97706">corrections</strong> before it can be approved.
                Please log in to the admin portal and update the relevant sections of your bio-data.
            </p>
            @if ($notes)
                <div class="notes-box">
                    <div class="notes-label">Reviewer's Notes</div>
                    <div class="notes-text">{{ $notes }}</div>
                </div>
            @endif
            <p style="font-size:14px;color:#475569;margin-top:16px">
                Once you've made the necessary corrections, notify HR so they can review your profile again.
            </p>
        @endif

        <p style="font-size:13px;color:#94a3b8;margin-top:24px;text-align:center">
            If you have any questions, please contact the HR department.
        </p>
    </div>
    <div class="footer">&copy; {{ date('Y') }} Printbuka. This message was sent automatically.</div>
</div>
</body>
</html>
