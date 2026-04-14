@extends('errors.layout')

@section('code', '401')
@section('heading', 'Sign in required')
@section('message', 'Please sign in before opening this page.')
@section('primary_action')
    <a href="{{ route('login') }}" class="inline-flex rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Sign In</a>
@endsection
