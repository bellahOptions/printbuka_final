@php
    $siteName = $siteSettings['site_name'] ?? config('app.name', 'Printbuka');
    $contactEmail = $siteSettings['contact_email'] ?? 'sales@printbuka.com.ng';
    $contactPhone = $siteSettings['contact_phone'] ?? '08035245784, 09054784526';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <title>@yield('code', 'Error') | {{ $siteName }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
        <main class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <section class="w-full max-w-3xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" class="h-12 w-auto" alt="{{ $siteName }} Logo">
                </a>

                <div class="mt-10">
                    <p class="text-sm font-black uppercase tracking-wider text-pink-700">@yield('code', 'Error')</p>
                    <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">@yield('heading', 'Something needs attention')</h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600">@yield('message', 'The request could not be completed right now.')</p>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    @hasSection('primary_action')
                        @yield('primary_action')
                    @else
                        <a href="{{ route('home') }}" class="inline-flex rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Go Home</a>
                    @endif
                    <a href="mailto:{{ $contactEmail }}" class="inline-flex rounded-md border border-slate-200 px-5 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Contact Support</a>
                </div>

                <div class="mt-8 rounded-md border border-slate-200 bg-slate-50 p-5 text-sm font-semibold text-slate-600">
                    <p>{{ $contactPhone }}</p>
                    <p class="mt-1">{{ $contactEmail }}</p>
                </div>
            </section>
        </main>
    </body>
</html>
