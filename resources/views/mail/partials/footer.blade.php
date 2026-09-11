@php
    $footerSettings = \App\Support\SiteSettings::all();
    $footerSiteName = trim((string) ($footerSettings['site_name'] ?? 'Printbuka'));
    $footerContactEmail = trim((string) ($footerSettings['contact_email'] ?? ''));
    $footerContactPhone = trim((string) ($footerSettings['contact_phone'] ?? ''));
    $footerNote = trim($__env->yieldContent('footerNote'));
@endphp
<tr>
    <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 28px;" class="email-footer">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-size:12px;line-height:1.6;color:#64748b;">
                    {{ $footerNote !== '' ? $footerNote : 'You are receiving this email because of your account or order activity with '.$footerSiteName.'.' }}
                </td>
            </tr>
            @if($footerContactEmail !== '' || $footerContactPhone !== '')
                <tr>
                    <td style="padding-top:8px;font-size:12px;color:#94a3b8;">
                        {{ $footerContactEmail }}{{ $footerContactEmail !== '' && $footerContactPhone !== '' ? ' · ' : '' }}{{ $footerContactPhone }}
                    </td>
                </tr>
            @endif
            <tr>
                <td style="padding-top:12px;font-size:11px;color:#cbd5e1;">
                    &copy; {{ date('Y') }} {{ $footerSiteName }}. All rights reserved.
                </td>
            </tr>
        </table>
    </td>
</tr>
