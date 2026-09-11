@extends('mail.layouts.base')

@php
    $settings  = \App\Support\SiteSettings::all();
    $siteName  = trim((string) ($settings['site_name'] ?? 'Printbuka'));
    $isShop    = $alertType === 'shop_order';
    $accentBg  = $isShop ? '#0f172a' : '#1e1b4b';
    $tagLabel  = $isShop ? 'SHOP ORDER' : 'QUOTE REQUEST';
    $adminUrl  = $isShop
        ? route('admin.shop-orders.show', $order)
        : route('admin.orders.show', $order);
@endphp

@section('title', $isShop ? 'New Shop Order' : 'New Quote Request')
@section('headerBadge', $tagLabel)
@section('headerTitle', $isShop ? 'New shop order received' : 'New quote request received')
@section('headerSubtitle', 'Hi '.($recipient->first_name ?? $recipient->displayName()).', action may be required.')

@section('content')
    {{-- Reference Banner (nested table: originally a sibling <tr> of the card-level table;
         now nested one level inside the shared layout's single content <td>, so it sits
         inset within the 26px content padding rather than flush with the card edges) --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;">
        <tr>
            <td style="background:#f1f5f9;padding:14px 28px;border-bottom:1px solid #e2e8f0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <p style="margin:0;font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Reference</p>
                            <p style="margin:4px 0 0;font-family:monospace;font-size:16px;font-weight:700;color:#0f172a;">
                                {{ $isShop ? $order->reference : $order->job_order_number }}
                            </p>
                        </td>
                        <td align="right">
                            <p style="margin:0;font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Received</p>
                            <p style="margin:4px 0 0;font-size:13px;font-weight:700;color:#0f172a;">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {!! $introHtml ?? '' !!}

    {{-- Customer info --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:20px;">
        <tr>
            <td colspan="2" style="background:#f8fafc;padding:10px 14px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;">Customer Information</p>
            </td>
        </tr>
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;width:40%;">Name</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;font-weight:700;">{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;">Email</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">{{ $order->customer_email }}</td>
        </tr>
        @if($order->customer_phone)
        <tr>
            <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#374151;">Phone</td>
            <td style="padding:10px 14px;font-size:13px;color:#0f172a;">{{ $order->customer_phone }}</td>
        </tr>
        @endif
    </table>

    {{-- Order-specific details --}}
    @if($isShop)
    {{-- SHOP ORDER DETAILS --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:20px;">
        <tr>
            <td colspan="2" style="background:#f8fafc;padding:10px 14px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;">Order Details</p>
            </td>
        </tr>
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;width:40%;">Items</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">
                @foreach($order->items as $item)
                    <span style="display:block;">{{ $item->quantity }}× {{ $item->product_name }}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;">Total</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:15px;font-weight:700;color:#16a34a;">NGN {{ number_format((float)$order->total, 0) }}</td>
        </tr>
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;">Payment</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#16a34a;font-weight:700;">{{ ucfirst($order->payment_status) }}</td>
        </tr>
        <tr>
            <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#374151;">Delivery To</td>
            <td style="padding:10px 14px;font-size:13px;color:#374151;">{{ $order->shipping_address }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}</td>
        </tr>
    </table>
    @else
    {{-- QUOTE REQUEST DETAILS --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:20px;">
        <tr>
            <td colspan="2" style="background:#f8fafc;padding:10px 14px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;">Quote Details</p>
            </td>
        </tr>
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;width:40%;">Job Type</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;font-weight:700;">{{ $order->job_type ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;">Quantity</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">{{ number_format((int)$order->quantity) }}</td>
        </tr>
        @if($order->quote_budget)
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;">Budget</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">NGN {{ number_format((float)$order->quote_budget, 0) }}</td>
        </tr>
        @endif
        @if($order->size_format)
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#374151;">Size / Format</td>
            <td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;font-size:13px;color:#0f172a;">{{ $order->size_format }}</td>
        </tr>
        @endif
        @if($order->delivery_city)
        <tr>
            <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#374151;">Delivery City</td>
            <td style="padding:10px 14px;font-size:13px;color:#0f172a;">{{ $order->delivery_city }}</td>
        </tr>
        @endif
    </table>
    @if($order->artwork_notes)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:8px;margin-bottom:20px;">
        <tr>
            <td style="padding:12px 16px;">
                <p style="margin:0 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;color:#6d28d9;letter-spacing:.5px;">Artwork Notes</p>
                <p style="margin:0;font-size:13px;color:#3730a3;line-height:1.6;">{{ $order->artwork_notes }}</p>
            </td>
        </tr>
    </table>
    @endif
    @endif

    {{-- CTA Button --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td align="center">
                <a href="{{ $adminUrl }}"
                   style="display:inline-block;padding:13px 32px;background:{{ $accentBg }};color:#ffffff;text-decoration:none;border-radius:7px;font-weight:700;font-size:14px;letter-spacing:.3px;">
                    {{ $isShop ? 'View Order in Admin →' : 'View Quote Request →' }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:12px;color:#94a3b8;text-align:center;line-height:1.6;">
        You received this alert because you have a role that requires visibility of new {{ $isShop ? 'shop orders' : 'quote requests' }}.<br>
        This is an automated message from {{ $siteName }}.
    </p>
    {!! $outroHtml ?? '' !!}
@endsection
