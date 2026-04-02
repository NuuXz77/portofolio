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
    <body class="min-h-screen bg-base-100">
        <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-12">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.22),transparent_45%),radial-gradient(circle_at_bottom_right,rgba(139,92,246,0.2),transparent_40%)]"></div>
            <div class="pointer-events-none absolute inset-0 opacity-25 [background-image:linear-gradient(rgba(148,163,184,0.1)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.1)_1px,transparent_1px)] [background-size:40px_40px]"></div>
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
