@extends('mail.layouts.base')

@section('title', 'Job Assignment')
@section('headerTitle', 'Job Assignment')

@section('content')
    {!! $introHtml ?? '' !!}
    <p>Hello {{ $designer->displayName() }},</p>
    <p>A new job has been assigned to you.</p>
    <p><strong>Job:</strong> {{ $order->job_order_number ?? $order->displayNumber() }}</p>
    <p><strong>Client:</strong> {{ $order->customer_name }}</p>
    <p><strong>Product:</strong> {{ $order->product?->name ?? ($order->job_type ?? 'Custom order') }}</p>
    <p><strong>Status:</strong> {{ $order->status }}</p>
    <p>Please review and begin work as soon as possible.</p>
    {!! $outroHtml ?? '' !!}
@endsection
