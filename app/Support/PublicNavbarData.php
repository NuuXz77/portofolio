<?php

namespace App\Support;

use App\Models\MenuItem;

class PublicNavbarData
{
    public static function brandName(string $fallback = 'Wisnu.dev'): string
    {
        $navbar = PortfolioContent::get('navbar', []);
        $brand = trim((string) ($navbar['logo_text'] ?? ''));

        return $brand !== '' ? $brand : $fallback;
    }

    public static function forJournal(): array
    {
        $navbar = PortfolioContent::get('navbar', []);

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
            'logoText' => self::brandName(),
            'navItems' => $navItems->all(),
            'ctaText' => (string) ($navbar['cta_text'] ?? 'Hire Me'),
            'ctaLink' => $ctaLinkRaw !== '' ? $ctaLinkRaw : $homeUrl,
        ];
    }
}
