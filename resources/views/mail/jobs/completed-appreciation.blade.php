@extends('mail.layouts.base')

@section('title', 'Thank You from Printbuka')
@section('headerBadge', 'PRINTBUKA')
@section('headerTitle', 'Your Job Has Been Completed')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Hello {{ $order->customer_name }},</p>
    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Your job <strong>{{ $order->job_order_number }}</strong> has been concluded by our operations team.</p>
    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;">Thank you for trusting Printbuka. We appreciate your business and look forward to serving you again.</p>
    {!! $outroHtml ?? '' !!}
    <p style="margin:0;font-size:14px;line-height:1.7;">Regards,<br>Printbuka Team</p>
@endsection
