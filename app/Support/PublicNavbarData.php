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
            'logoText' => trim((string) ($navbar['logo_text'] ?? 'Wisnu.dev')),
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

        $menuItems = MenuItem::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $homeUrl = route('home');
        $journalUrl = route('journal.index');

        $navItems = $menuItems->isNotEmpty()
            ? $menuItems->map(fn ($item) => ['href' => (string) $item->href, 'label' => (string) $item->label])->all()
            : [
                ['href' => '#home', 'label' => 'Home'],
                ['href' => '#about', 'label' => 'About'],
                ['href' => '#skills', 'label' => 'Skills'],
                ['href' => '#projects', 'label' => 'Projects'],
                ['href' => '#experience', 'label' => 'Experience'],
                ['href' => '#contact', 'label' => 'Contact'],
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
            ->reject(function (array $item) use ($journalUrl): bool {
                $label = trim((string) ($item['label'] ?? ''));
                $href = trim((string) ($item['href'] ?? ''));

                if (strcasecmp($label, 'Journal') === 0) {
                    return true;
                }

                if ($href === '') {
                    return false;
                }

                if (strcasecmp($href, '#journal') === 0 || strcasecmp($href, $journalUrl) === 0) {
                    return true;
                }

                $targetPath = trim((string) parse_url($href, PHP_URL_PATH), '/');
                $journalPath = trim((string) parse_url($journalUrl, PHP_URL_PATH), '/');

                return $targetPath !== '' && $targetPath === $journalPath;
            })
            ->values();

        $navItems->push([
            'href' => $journalUrl,
            'label' => 'Journal',
        ]);

        $ctaLinkRaw = trim((string) ($navbar['cta_link'] ?? '#contact'));

        if ($ctaLinkRaw !== '' && str_starts_with($ctaLinkRaw, '#')) {
            $ctaLinkRaw = $homeUrl.$ctaLinkRaw;
        }

        return [
            ...$brandConfig,
            'navItems' => $navItems->all(),
            'ctaText' => (string) ($navbar['cta_text'] ?? 'Hire Me'),
            'ctaLink' => $ctaLinkRaw !== '' ? $ctaLinkRaw : $homeUrl,
        ];
    }
}
