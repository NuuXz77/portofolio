<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Wisnu.dev | Fullstack Web Developer' }}</title>
        <meta name="description" content="Premium personal portfolio of a Fullstack Web Developer focused on scalable web apps, API development, and DevOps deployment.">
        <meta name="keywords" content="fullstack developer, laravel developer, nextjs developer, devops engineer, portfolio">
        <meta name="author" content="Wisnu.dev">
        <meta name="robots" content="index, follow">

        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $title ?? 'Wisnu.dev | Fullstack Web Developer' }}">
        <meta property="og:description" content="Building scalable web applications with modern technologies like Laravel, Next.js, and DevOps practices.">
        <meta property="og:image" content="https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?auto=format&fit=crop&w=1600&q=80">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="portfolio-root bg-base-100 text-base-content antialiased">
        {{ $slot }}

        @livewireScripts
    </body>
</html>
