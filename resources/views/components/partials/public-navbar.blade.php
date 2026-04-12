@props([
    'logoText' => 'Wisnu.dev',
    'brandMode' => 'text',
    'brandLogoType' => 'image',
    'brandLogoImage' => null,
    'brandLogoIcon' => 'sparkles',
    'brandHref' => '/',
    'navItems' => [],
    'ctaText' => 'Hire Me',
    'ctaLink' => '#contact',
    'showCta' => true,
])

@php
    $isSpecialScheme = static fn (string $href): bool => str_starts_with($href, 'mailto:')
        || str_starts_with($href, 'tel:');

    $isAbsoluteHttp = static fn (string $href): bool => str_starts_with($href, 'http://')
        || str_starts_with($href, 'https://');

    $isHashOnly = static fn (string $href): bool => str_starts_with($href, '#');

    $currentHost = request()->getHost();
    $currentScheme = request()->getScheme();
    $currentPath = trim(request()->path(), '/');

    $isSameOrigin = static function (string $href) use ($currentHost, $currentScheme, $isAbsoluteHttp): bool {
        if (! $isAbsoluteHttp($href)) {
            return true;
        }

        $targetHost = parse_url($href, PHP_URL_HOST);
        $targetScheme = parse_url($href, PHP_URL_SCHEME);

        return $targetHost === $currentHost && $targetScheme === $currentScheme;
    };

    $shouldNavigate = static fn (string $href): bool => ! $isSpecialScheme($href)
        && ! $isHashOnly($href)
        && $isSameOrigin($href);

    $isActive = static function (string $href) use ($currentPath, $isHashOnly, $isSpecialScheme, $isSameOrigin): bool {
        if ($isSpecialScheme($href) || $isHashOnly($href) || ! $isSameOrigin($href)) {
            return false;
        }

        $targetPath = trim((string) parse_url($href, PHP_URL_PATH), '/');

        if ($targetPath === '') {
            return $currentPath === '';
        }

        return $currentPath === $targetPath || str_starts_with($currentPath, $targetPath.'/');
    };

    $resolvedLogoText = trim((string) $logoText);
    $resolvedBrandMode = in_array((string) $brandMode, ['text', 'logo', 'combo'], true) ? (string) $brandMode : 'text';
    $resolvedBrandLogoType = in_array((string) $brandLogoType, ['image', 'icon'], true) ? (string) $brandLogoType : 'image';
    $resolvedBrandLogoIcon = trim((string) $brandLogoIcon);
    $resolvedBrandLogoImage = trim((string) ($brandLogoImage ?? ''));

    $brandImageUrl = '';

    if ($resolvedBrandLogoImage !== '') {
        $brandImageUrl = $isAbsoluteHttp($resolvedBrandLogoImage)
            ? $resolvedBrandLogoImage
            : \Illuminate\Support\Facades\Storage::url($resolvedBrandLogoImage);
    }

    $showBrandText = in_array($resolvedBrandMode, ['text', 'combo'], true);
    $showBrandMedia = in_array($resolvedBrandMode, ['logo', 'combo'], true);

    $hasBrandImage = $resolvedBrandLogoType === 'image' && $brandImageUrl !== '';
    $hasBrandIcon = $resolvedBrandLogoType === 'icon' && $resolvedBrandLogoIcon !== '';
    $hasBrandMedia = $showBrandMedia && ($hasBrandImage || $hasBrandIcon);

    if (! $showBrandText && ! $hasBrandMedia) {
        $showBrandText = true;
    }

    if ($showBrandText && $resolvedLogoText === '') {
        $resolvedLogoText = 'Wisnu.dev';
    }
@endphp

<header id="top" class="fixed inset-x-0 top-0 z-50 px-4 pt-4 sm:px-8">
    <div class="relative mx-auto max-w-7xl">
        <nav class="portfolio-glass glass-surface flex w-full items-center justify-between rounded-2xl border border-base-content/15 px-4 py-3 shadow-2xl backdrop-blur-xl sm:px-6" aria-label="Primary navigation">
            <a
                href="{{ $brandHref }}"
                @if($shouldNavigate($brandHref)) wire:navigate @endif
                class="inline-flex min-w-0 items-center gap-2 text-xl font-semibold tracking-tight text-base-content"
                aria-label="{{ $resolvedLogoText !== '' ? $resolvedLogoText : 'Brand' }}"
            >
                @if ($hasBrandMedia)
                    @if ($hasBrandImage)
                        <img src="{{ $brandImageUrl }}" alt="Brand logo" class="h-8 w-8 shrink-0 rounded-lg border border-white/10 object-cover sm:h-9 sm:w-9">
                    @else
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-info/35 bg-info/15 text-info sm:h-9 sm:w-9">
                            <i data-lucide="{{ $resolvedBrandLogoIcon }}" class="h-4 w-4 sm:h-5 sm:w-5"></i>
                        </span>
                    @endif
                @endif

                @if ($showBrandText)
                    <span class="truncate">{{ $resolvedLogoText }}</span>
                @endif
            </a>

            <div class="hidden items-center gap-2 lg:flex">
                @foreach ($navItems as $item)
                    @php
                        $href = (string) ($item['href'] ?? '#');
                        $active = $isActive($href);
                    @endphp
                    <a href="{{ $href }}" @if($shouldNavigate($href)) wire:navigate @endif class="btn btn-ghost btn-sm rounded-xl {{ $active ? 'bg-info/15 text-info' : 'text-base-content/80 hover:bg-base-content/10 hover:text-base-content' }}">
                        {{ $item['label'] ?? 'Link' }}
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-2 lg:flex">
                <label class="swap swap-rotate btn btn-circle btn-ghost" aria-label="Toggle color theme">
                    <input id="theme-toggle" type="checkbox" aria-label="Toggle color theme">
                    <i data-lucide="sun" class="swap-on h-5 w-5"></i>
                    <i data-lucide="moon" class="swap-off h-5 w-5"></i>
                </label>

                @if ($showCta)
                    <a href="{{ $ctaLink }}" @if($shouldNavigate($ctaLink)) wire:navigate @endif class="btn btn-info rounded-xl px-5 text-base-content">{{ $ctaText }}</a>
                @endif
            </div>

            <button id="mobile-menu-toggle" class="btn btn-ghost btn-circle lg:hidden" aria-label="Open mobile menu" aria-expanded="false" aria-controls="mobile-menu-panel">
                <i data-lucide="menu" class="h-5 w-5"></i>
            </button>
        </nav>

        <div id="mobile-menu-panel" class="portfolio-glass glass-surface w-full rounded-2xl border border-transparent px-4 lg:hidden">
            <div class="py-4">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-base-content/70">Navigation</span>
                    <label class="swap swap-rotate btn btn-circle btn-ghost btn-sm" aria-label="Toggle color theme">
                        <input id="theme-toggle-mobile" type="checkbox" aria-label="Toggle color theme">
                        <i data-lucide="sun" class="swap-on h-4 w-4"></i>
                        <i data-lucide="moon" class="swap-off h-4 w-4"></i>
                    </label>
                </div>

                <div class="grid gap-2">
                    @foreach ($navItems as $item)
                        @php($mobileHref = (string) ($item['href'] ?? '#'))
                        <a href="{{ $mobileHref }}" @if($shouldNavigate($mobileHref)) wire:navigate @endif class="mobile-nav-link btn btn-ghost justify-start rounded-xl text-base-content/80 hover:bg-base-content/10 hover:text-base-content">
                            {{ $item['label'] ?? 'Link' }}
                        </a>
                    @endforeach

                    @if ($showCta)
                        <a href="{{ $ctaLink }}" @if($shouldNavigate($ctaLink)) wire:navigate @endif class="mobile-nav-link btn btn-info mt-1 rounded-xl text-base-content">{{ $ctaText }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

<div aria-hidden="true" class="h-10 sm:h-20"></div>
