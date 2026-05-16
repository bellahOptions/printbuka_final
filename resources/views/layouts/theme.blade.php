<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light;">
    <head>
        @php
            $siteName = $siteSettings['site_name'] ?? config('app.name', 'Printbuka');
            $pageTitle = trim(strip_tags($__env->yieldContent('title', $title ?? $siteName)));
            $defaultDescription = "Printbuka is Nigeria's online print shop for quality printing, branded gifts, and specialist production services.";
            $metaDescription = trim(strip_tags($__env->yieldContent('meta_description', $defaultDescription)));
            $canonicalUrl = $__env->yieldContent('canonical_url', url()->current());
            $metaRobots = $__env->yieldContent('meta_robots', 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1');
            $ogType = $__env->yieldContent('og_type', 'website');
            $ogImage = $__env->yieldContent('og_image', asset('logo.png'));
            $twitterCard = $__env->yieldContent('twitter_card', 'summary_large_image');

            $organizationSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => url('/'),
                'email' => $siteSettings['contact_email'] ?? null,
                'telephone' => $siteSettings['contact_phone'] ?? null,
            ];

            $websiteSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => url('/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => route('products.index').'?search={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ];
        @endphp

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="light">
        <meta name="supported-color-schemes" content="light">
        <meta name="description" content="{{ $metaDescription }}">
        <meta name="robots" content="{{ $metaRobots }}">
        <meta name="author" content="{{ $siteName }}">

        <link rel="canonical" href="{{ $canonicalUrl }}">
        <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}">
        <link rel="alternate" type="text/plain" title="LLMs" href="{{ route('llms') }}">

        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:image" content="{{ $ogImage }}">

        <meta name="twitter:card" content="{{ $twitterCard }}">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <title>{{ $pageTitle }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @stack('head')
    </head>
    <body class="bg-slate-50 text-slate-950 antialiased">
        <x-public-advertisements :placements="['top_banner', 'floating_card']" />
        @include('layouts.guest.nav')
        @include('layouts.partials.breadcrumbs', ['rootLabel' => 'Home', 'rootRoute' => 'home'])
        @yield('content')
        <x-public-advertisements :placements="['inline_banner', 'footer_banner']" />
        @include('layouts.guest.footer')
        <x-turnstile-auto />
        <x-form-icons />
        @livewireScripts
    </body>
</html>
