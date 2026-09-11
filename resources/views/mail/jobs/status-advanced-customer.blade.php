@extends('mail.layouts.base')

@section('title', 'Order Status Update')
@section('headerTitle', 'Order Status Update')

@section('content')
    {!! $introHtml ?? '' !!}
    <p>Hello {{ $order->customer_name }},</p>
    <p>Your order has advanced to a new stage.</p>
    <p><strong>Order:</strong> {{ $order->job_order_number ?? $order->displayNumber() }}</p>
    <p><strong>Previous Status:</strong> {{ $oldStatus }}</p>
    <p><strong>Current Status:</strong> {{ $newStatus }}</p>
    <p>We will keep notifying you as your job progresses.</p>
    {!! $outroHtml ?? '' !!}
@endsection
