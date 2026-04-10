@props([
    'code' => '404',
    'title' => 'Page Not Found',
    'description' => "The page you're looking for doesn't exist or has been moved.",
    'icon' => 'search-x',
    'homeUrl' => null,
    'homeLabel' => 'Back to Home',
    'showBack' => true,
    'backLabel' => 'Go Back',
])

@php
    $resolvedHomeUrl = $homeUrl ?: route('home');
    $slotContent = trim((string) $slot);
@endphp

<section class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-8">
    <div class="w-full max-w-3xl">
        <article class="glass-card error-fade-in rounded-2xl border border-base-content/12 bg-base-100/70 p-7 text-center shadow-2xl sm:p-10">
            <div class="mx-auto mb-5 inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-info/30 bg-info/12 text-info">
                <i data-lucide="{{ $icon }}" class="h-6 w-6"></i>
            </div>

            <p class="error-code error-code-float error-code-glow bg-linear-to-r from-cyan-300 via-sky-400 to-blue-500 bg-clip-text text-6xl font-extrabold tracking-tight text-transparent sm:text-7xl md:text-8xl">
                {{ $code }}
            </p>

            <h1 class="mt-4 text-2xl font-bold text-base-content sm:text-3xl">{{ $title }}</h1>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-base-content/70 sm:text-base">{{ $description }}</p>

            @if ($slotContent !== '')
                <div class="mt-4 rounded-xl border border-base-content/10 bg-base-200/45 px-4 py-3 text-sm text-base-content/75">
                    {{ $slot }}
                </div>
            @endif

            <div class="mt-7 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ $resolvedHomeUrl }}" class="btn btn-info rounded-xl px-6 text-base-content transition-all duration-200 hover:scale-[1.02]">
                    {{ $homeLabel }}
                </a>

                @if ($showBack)
                    <button type="button" onclick="window.history.back()" class="btn btn-ghost rounded-xl border border-base-content/20 px-6 text-base-content/85 transition-all duration-200 hover:scale-[1.02] hover:bg-base-content/10">
                        {{ $backLabel }}
                    </button>
                @endif
            </div>
        </article>
    </div>
</section>
