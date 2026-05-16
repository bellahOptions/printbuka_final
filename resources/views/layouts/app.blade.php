<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">

        <title>{{ $title ?? ($siteSettings['site_name'] ?? config('app.name')) }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-slate-50 text-slate-950 antialiased">
        {{ $slot }}

        <x-turnstile-auto />
        <x-form-icons />
        @livewireScripts
    </body>
</html>
