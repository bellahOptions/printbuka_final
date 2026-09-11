@extends('mail.layouts.base')

@section('title', 'Printbuka Job Finance Summary')
@section('headerTitle', 'Printbuka Job Finance Summary')

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin-bottom: 16px;">Hello {{ $order->customer_name }},</p>
    <p style="margin-bottom: 16px;">Your job <strong>{{ $order->job_order_number }}</strong> has been marked as delivered. Attached is the finance summary for this job, including the expense entries recorded against it.</p>
    <p style="margin-bottom: 16px;">If you have any questions about this job or the attached finance record, feel free to reply to this email.</p>
    <p style="margin-bottom: 16px;">Thank you for choosing Printbuka.</p>
    {!! $outroHtml ?? '' !!}
    <p style="margin-bottom: 0;">Regards,<br>Printbuka Team</p>
@endsection
