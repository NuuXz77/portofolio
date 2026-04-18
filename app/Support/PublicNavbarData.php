<?php

namespace App\Support;

use App\Models\MenuItem;

class PublicNavbarData
{
    /**
     * @return array{logoText: string, brandMode: string, brandLogoType: string, brandLogoImage: ?string, brandLogoIcon: string}
     */
    public static function brandConfig(?array $navbar = null): array
    {
        $navbar ??= PortfolioContent::get('navbar', []);

        $rawBrandMode = (string) ($navbar['brand_mode'] ?? 'text');
        $rawBrandLogoType = (string) ($navbar['brand_logo_type'] ?? 'image');

        $brandMode = in_array($rawBrandMode, ['text', 'logo', 'combo'], true)
            ? $rawBrandMode
            : 'text';

        $brandLogoType = in_array($rawBrandLogoType, ['image', 'icon'], true)
            ? $rawBrandLogoType
            : 'image';

        return [
            'logoText' => LocalizedContent::resolve($navbar['logo_text'] ?? 'Wisnu.dev', default: 'Wisnu.dev'),
            'brandMode' => $brandMode,
            'brandLogoType' => $brandLogoType,
            'brandLogoImage' => isset($navbar['brand_logo_image']) ? trim((string) $navbar['brand_logo_image']) : null,
            'brandLogoIcon' => trim((string) ($navbar['brand_logo_icon'] ?? 'sparkles')),
        ];
    }

    public static function brandName(string $fallback = 'Wisnu.dev'): string
    {
        $brandConfig = self::brandConfig();
        $brand = trim($brandConfig['logoText'] ?? '');

        return $brand !== '' ? $brand : $fallback;
    }

    public static function forJournal(): array
    {
        $navbar = PortfolioContent::get('navbar', []);
        $brandConfig = self::brandConfig($navbar);
        $supportedLocales = (array) config('app.supported_locales', ['id', 'en']);

        $menuItems = MenuItem::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $homeUrl = route('home');
        $journalUrl = route('journal.index');
        $normalizePath = static function (string $href) use ($supportedLocales): string {
            $path = trim((string) parse_url($href, PHP_URL_PATH), '/');

            if ($path === '') {
                return '';
            }

            $segments = explode('/', $path);

            if ($segments !== [] && in_array((string) ($segments[0] ?? ''), $supportedLocales, true)) {
                array_shift($segments);
            }

            return trim(implode('/', $segments), '/');
        };
        $journalLabel = strtolower(trim((string) __('navigation.journal')));
        $journalPath = $normalizePath($journalUrl);

        $navItems = $menuItems->isNotEmpty()
            ? $menuItems->map(fn ($item) => [
                'href' => (string) $item->href,
                'label' => LocalizedContent::resolve($item->label, default: __('navigation.home')),
            ])->all()
            : [
                ['href' => '#home', 'label' => __('navigation.home')],
                ['href' => '#about', 'label' => __('navigation.about')],
                ['href' => '#skills', 'label' => __('navigation.skills')],
                ['href' => '#projects', 'label' => __('navigation.projects')],
                ['href' => '#experience', 'label' => __('navigation.experience')],
                ['href' => '#contact', 'label' => __('navigation.contact')],
            ];

        $navItems = collect($navItems)
            ->map(function (array $item) use ($homeUrl): array {
                $href = trim((string) ($item['href'] ?? ''));
                $label = trim((string) ($item['label'] ?? 'Link'));

                if ($href !== '' && str_starts_with($href, '#')) {
                    $href = $homeUrl.$href;
                }

                return [
                    'href' => $href,
                    'label' => $label,
                ];
            })
            ->reject(function (array $item) use ($journalUrl, $normalizePath, $journalLabel, $journalPath): bool {
                $label = trim((string) ($item['label'] ?? ''));
                $href = trim((string) ($item['href'] ?? ''));
                $normalizedLabel = strtolower($label);

                if ($normalizedLabel === 'journal' || $normalizedLabel === $journalLabel) {
                    return true;
                }

                if ($href === '') {
                    return false;
                }

                if (strcasecmp($href, '#journal') === 0 || strcasecmp($href, $journalUrl) === 0) {
                    return true;
                }

                $targetPath = $normalizePath($href);

                if ($targetPath === '') {
                    return false;
                }

                return $targetPath === 'journal' || $targetPath === $journalPath;
            })
            ->values();

        $navItems->push([
            'href' => $journalUrl,
            'label' => __('navigation.journal'),
        ]);

        $ctaLinkRaw = trim((string) ($navbar['cta_link'] ?? '#contact'));

        if ($ctaLinkRaw !== '' && str_starts_with($ctaLinkRaw, '#')) {
            $ctaLinkRaw = $homeUrl.$ctaLinkRaw;
        }

        return [
            ...$brandConfig,
            'navItems' => $navItems->all(),
            'ctaText' => LocalizedContent::resolve($navbar['cta_text'] ?? __('navigation.hire_me'), default: __('navigation.hire_me')),
            'ctaLink' => $ctaLinkRaw !== '' ? $ctaLinkRaw : $homeUrl,
        ];
    }
}
