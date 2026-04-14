@php($statusCode = isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500)

@extends('errors.layout')

@section('code', (string) $statusCode)
@section('heading', 'Service error')
@section('message', 'The service could not complete the request right now. Please try again shortly.')
