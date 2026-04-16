@extends('errors.layout')

@section('code', '503')
@section('heading', 'Maintenance in progress')
@section('message')
    {{ $message ?? 'We are making a few improvements. Please check back shortly.' }}
@endsection
@section('primary_action')
    <a href="{{ route('home') }}" class="inline-flex rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Back to Home</a>
@endsection
