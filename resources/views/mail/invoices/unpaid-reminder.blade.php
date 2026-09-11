@extends('mail.layouts.base')

@section('title', 'Unpaid '.$invoice->documentTypeLabel().' Reminder')
@section('headerTitle', 'Payment reminder')
@section('headerSubtitle', $invoice->documentTypeLabel().' '.$invoice->invoice_number)

@section('content')
    {!! $introHtml ?? '' !!}
    <p style="margin:0 0 16px;">Hello {{ $invoice->order?->customer_name ?? 'there' }},</p>
    <p style="margin:0 0 16px;">This is a friendly reminder that your Printbuka {{ strtolower($invoice->documentTypeLabel()) }} of NGN {{ number_format((float) $invoice->total_amount, 2) }} is still unpaid.</p>
    <p style="margin:0 0 16px;">Due date: <strong>{{ $invoice->due_at?->format('M d, Y h:i A') ?? 'To be confirmed' }}</strong></p>
    <p style="margin:0;">Please use {{ $invoice->invoice_number }} as your payment reference.</p>
    {!! $outroHtml ?? '' !!}
@endsection
