@php
    $siteName = trim((string) (\App\Support\SiteSettings::all()['site_name'] ?? 'Printbuka'));
@endphp
<tr>
    <td style="background:#0f172a;padding:24px 28px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <img src="{{ $message->embed(public_path('logo-dark.svg')) }}" alt="{{ $siteName }}" width="130" style="display:block;height:auto;">
                </td>
                @if(!empty($headerBadge))
                <td align="right">
                    <span style="background:#EC268F;color:#fff;font-size:10px;font-weight:700;padding:4px 12px;border-radius:20px;letter-spacing:1px;">{{ strtoupper($headerBadge) }}</span>
                </td>
                @endif
            </tr>
        </table>
        @if(!empty($headerTitle))
            <h1 style="margin:18px 0 4px;font-size:22px;color:#ffffff;line-height:1.2;">{{ $headerTitle }}</h1>
        @endif
        @if(!empty($headerSubtitle))
            <p style="margin:0;color:#94a3b8;font-size:13px;">{{ $headerSubtitle }}</p>
        @endif
    </td>
</tr>
