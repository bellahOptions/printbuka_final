<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>@yield('title', $title ?? config('app.name'))</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:ital,wght@0,300;0,400;0,500;1,300;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            *{
            font-family: "DM Sans", sans-serif;
            font-optical-sizing: auto;
            }
            h1{
                font-family: "DM Serif Display", serif;
                font-weight: 400;
            }
        </style>
    </head>
    <body class="tracking-tight">
        @include('layouts.guest.nav')
        @yield('content')
        @include('layouts.guest.footer')
    </body>
</html>
