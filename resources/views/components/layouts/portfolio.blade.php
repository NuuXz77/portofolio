<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">
    @php
        $seo = \App\Support\PortfolioContent::get('seo', []);
        $navbar = \App\Support\PortfolioContent::get('navbar', []);
        $brandName = trim((string) ($navbar['logo_text'] ?? 'Wisnu.dev'));

        if ($brandName === '') {
            $brandName = 'Wisnu.dev';
        }

        $seoTitle = trim((string) ($seo['title'] ?? ''));
        $isSeedSeoTitle = strcasecmp($seoTitle, 'Wisnu.dev | Fullstack Web Developer') === 0;

        $defaultTitle = $seoTitle !== '' && !$isSeedSeoTitle ? $seoTitle : $brandName . ' | Fullstack Web Developer';
        $defaultDescription =
            (string) ($seo['description'] ??
                'Premium personal portfolio of a Fullstack Web Developer focused on scalable web apps, API development, and DevOps deployment.');
        $defaultKeywords =
            (string) ($seo['keywords'] ??
                'fullstack developer, laravel developer, nextjs developer, devops engineer, portfolio');
        $defaultAuthor = $brandName;

        $metaTitle = trim((string) ($title ?? $defaultTitle));
        $metaDescription = trim((string) ($description ?? $defaultDescription));
        $metaKeywords = trim((string) ($keywords ?? $defaultKeywords));
        $metaAuthor = trim((string) ($author ?? $defaultAuthor));

        $metaOgTitle = trim((string) ($ogTitle ?? $metaTitle));
        $metaOgDescription = trim((string) ($ogDescription ?? $metaDescription));
        $metaOgImage = trim(
            (string) ($ogImage ??
                'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?auto=format&fit=crop&w=1600&q=80'),
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
            $metaAuthor = $brandName;
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
    <meta name="simple-icons-cdn" content="{{ $simpleIconsCdn }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaOgTitle }}">
    <meta property="og:description" content="{{ $metaOgDescription }}">
    <meta property="og:image" content="{{ $metaOgImage }}">

    <script>
        window.__appCdn = Object.assign({}, window.__appCdn || {}, {
            simpleIcons: @js($simpleIconsCdn),
        });
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="portfolio-root bg-base-100 text-base-content antialiased">
    <div id="portfolio" data-portfolio-root class="relative overflow-x-clip bg-base-100 text-base-content">
        <div aria-hidden="true" class="portfolio-background pointer-events-none" data-parallax-layer="background" data-parallax-speed="0.05" data-parallax-max="46">
            <div class="portfolio-grid" data-parallax-layer="background" data-parallax-speed="0.035" data-parallax-max="38"></div>
            <div class="portfolio-glow portfolio-glow-top-left" data-parallax-layer="background" data-parallax-speed="0.03" data-parallax-max="44"></div>
            <div class="portfolio-glow portfolio-glow-bottom-right" data-parallax-layer="background" data-parallax-speed="0.055" data-parallax-max="56"></div>
            <div class="portfolio-glow portfolio-glow-center" data-parallax-layer="background" data-parallax-speed="0.04" data-parallax-max="40"></div>
            <div class="portfolio-noise"></div>
            <div class="portfolio-vignette"></div>
        </div>
        <div id="scroll-progress"
            class="fixed left-0 top-0 z-50 h-1 w-0 bg-linear-to-r from-cyan-400 via-blue-500 to-violet-500 shadow-[0_0_14px_rgba(80,170,255,0.75)] transition-[width] duration-150">
        </div>


        <div class="relative z-10">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>

</html>
