<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin Authentication' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="premium-bg-root premium-bg-auth min-h-screen bg-base-100 text-base-content">
        <div aria-hidden="true" class="premium-background pointer-events-none">
            <div class="premium-grid"></div>
            <div class="premium-glow premium-glow-top-left"></div>
            <div class="premium-glow premium-glow-bottom-right"></div>
            <div class="premium-glow premium-glow-center"></div>
            <div class="premium-noise"></div>
            <div class="premium-vignette"></div>
        </div>

        <div class="relative z-10 flex min-h-screen items-center justify-center overflow-hidden px-4 py-12">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
