<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light;">
<head>
    @php($siteName = $siteSettings['site_name'] ?? config('app.name', 'Printbuka'))
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', $siteName)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-white text-slate-950">
    @yield('content')
    @livewireScripts
    @stack('scripts')
</body>
</html>
