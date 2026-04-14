@extends('errors.layout')

@section('code', '419')
@section('heading', 'Session expired')
@section('message', 'Your secure session expired. Refresh the page and submit the form again.')
@section('primary_action')
    <a href="{{ url()->previous() ?: route('home') }}" class="inline-flex rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Try Again</a>
@endsection
