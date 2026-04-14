@php($statusCode = isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400)

@extends('errors.layout')

@section('code', (string) $statusCode)
@section('heading', 'Request error')
@section('message', 'The request could not be completed. Please check the link and try again.')
