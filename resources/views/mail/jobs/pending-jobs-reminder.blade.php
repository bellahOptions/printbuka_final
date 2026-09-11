@extends('mail.layouts.base')

@section('title', 'Pending Jobs Reminder')
@section('headerTitle', 'Pending Jobs Reminder')

@section('content')
    {!! $introHtml ?? '' !!}
    <p>Hello {{ $recipient->displayName() }},</p>
    <p>You have paid pending jobs/tasks that need attention:</p>
    <ul>
        @foreach ($items as $item)
            <li>
                <strong>{{ $item['order']->job_order_number ?? $item['order']->displayNumber() }}</strong>
                - {{ $item['order']->customer_name }}
                - {{ $item['phase'] }}
                - {{ $item['status'] }}
                - {{ $item['payment_status'] }}
                - stuck for about {{ $item['stuck_hours'] }} hour(s)
                <br>
                Task: {{ $item['task'] }}
                <br>
                <a href="{{ route('admin.orders.show', $item['order']) }}">Open job</a>
            </li>
        @endforeach
    </ul>
    <p>Please update these jobs in the admin dashboard.</p>
    {!! $outroHtml ?? '' !!}
@endsection
