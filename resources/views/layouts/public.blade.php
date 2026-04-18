<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/favicon/android-chrome-192x192.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('images/favicon/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon/favicon-16x16.png')}}">
    <link rel="manifest" href="{{asset('images/favicon/site.webmanifest')}}">
    @php
        $seo = \App\Support\PortfolioContent::get('seo', []);
        $navbar = \App\Support\PortfolioContent::get('navbar', []);
        $brandName = trim((string) \App\Support\LocalizedContent::resolve($navbar['logo_text'] ?? 'Wisnu.dev', default: 'Wisnu.dev'));

        if ($brandName === '') {
            $brandName = 'Wisnu.dev';
        }

        $seoTitle = trim((string) \App\Support\LocalizedContent::resolve($seo['title'] ?? '', default: ''));
        $isSeedSeoTitle = strcasecmp($seoTitle, 'Wisnu.dev | Fullstack Web Developer') === 0;

        $defaultTitle = $seoTitle !== '' && !$isSeedSeoTitle ? $seoTitle : $brandName . ' | Fullstack Web Developer';

        $defaultDescription = trim((string) \App\Support\LocalizedContent::resolve(
            $seo['description'] ?? null,
            default: 'Premium personal portfolio of a Fullstack Web Developer focused on scalable web apps, API development, and DevOps deployment.'
        ));
        $defaultKeywords = trim((string) \App\Support\LocalizedContent::resolve(
            $seo['keywords'] ?? null,
            default: 'fullstack developer, laravel developer, nextjs developer, devops engineer, portfolio'
        ));
        $defaultAuthor = $brandName;

        $metaTitle = trim((string) $__env->yieldContent('title', $defaultTitle));
        $metaDescription = trim((string) $__env->yieldContent('description', $defaultDescription));
        $metaKeywords = trim((string) $__env->yieldContent('keywords', $defaultKeywords));
        $metaAuthor = trim((string) $__env->yieldContent('author', $defaultAuthor));

        $metaOgTitle = trim((string) $__env->yieldContent('og_title', $metaTitle));
        $metaOgDescription = trim((string) $__env->yieldContent('og_description', $metaDescription));
        $metaOgImage = trim(
            (string) $__env->yieldContent(
                'og_image',
                'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?auto=format&fit=crop&w=1600&q=80',
            ),
        );

        if ($metaTitle === '') {
            $metaTitle = $defaultTitle;
        }

        if ($metaDescription === '') {
            $metaDescription = $defaultDescription;
        }

        if ($metaKeywords === '') {
            $metaKeywords = $defaultKeywords;
        }

        if ($metaAuthor === '') {
            $metaAuthor = $defaultAuthor;
        }

        if ($metaOgTitle === '') {
            $metaOgTitle = $metaTitle;
        }

        if ($metaOgDescription === '') {
            $metaOgDescription = $metaDescription;
        }

        if ($metaOgImage === '') {
            $metaOgImage =
                'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?auto=format&fit=crop&w=1600&q=80';
        }
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="author" content="{{ $metaAuthor }}">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaOgTitle }}">
    <meta property="og:description" content="{{ $metaOgDescription }}">
    <meta property="og:image" content="{{ $metaOgImage }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="portfolio-root bg-base-100 text-base-content antialiased">
    <div id="portfolio" data-portfolio-root class="relative overflow-x-clip bg-base-100 text-base-content">
        <div aria-hidden="true" class="portfolio-background pointer-events-none">
            <div class="portfolio-grid"></div>
            <div class="portfolio-glow portfolio-glow-top-left"></div>
            <div class="portfolio-glow portfolio-glow-bottom-right"></div>
            <div class="portfolio-glow portfolio-glow-center"></div>
            <div class="portfolio-noise"></div>
            <div class="portfolio-vignette"></div>
        </div>

        <div class="relative z-10">
            @yield('content')
        </div>
    </div>
</body>

</html>
