<?php

namespace App\Support;

class LocalizedContent
{
    /**
     * @param  mixed  $value
     */
    public static function resolve(mixed $value, ?string $locale = null, ?string $fallbackLocale = null, string $default = ''): string
    {
        $locale = trim((string) ($locale ?? app()->getLocale()));
        $fallbackLocale = trim((string) ($fallbackLocale ?? config('app.fallback_locale', 'id')));

        $translations = self::toTranslations($value);

        if ($translations !== null) {
            $localized = trim((string) ($translations[$locale] ?? ''));
            if ($localized !== '') {
                return $localized;
            }

            $fallback = trim((string) ($translations[$fallbackLocale] ?? ''));
            if ($fallback !== '') {
                return $fallback;
            }

            foreach ($translations as $text) {
                $candidate = trim((string) $text);
                if ($candidate !== '') {
                    return $candidate;
                }
            }

            return $default;
        }

        $stringValue = trim((string) $value);

        return $stringValue !== '' ? $stringValue : $default;
    }

    /**
     * @param  mixed  $value
     * @return array{id: string, en: string}
     */
    public static function split(mixed $value): array
    {
        $translations = self::toTranslations($value);

        if ($translations === null) {
            $scalar = trim((string) $value);

            return [
                'id' => $scalar,
                'en' => $scalar,
            ];
        }

        $id = trim((string) ($translations['id'] ?? ''));
        $en = trim((string) ($translations['en'] ?? ''));

        if ($id === '' && $en !== '') {
            $id = $en;
        }

        if ($en === '' && $id !== '') {
            $en = $id;
        }

        return [
            'id' => $id,
            'en' => $en,
        ];
    }

    /**
     * @return array{id: string, en: string}
     */
    public static function pack(string $id, string $en): array
    {
        $id = trim($id);
        $en = trim($en);

        if ($id === '' && $en !== '') {
            $id = $en;
        }

        if ($en === '' && $id !== '') {
            $en = $id;
        }

        return [
            'id' => $id,
            'en' => $en,
        ];
    }

    /**
     * @param  mixed  $value
     * @return array<string, string>|null
     */
    public static function toTranslations(mixed $value): ?array
    {
        if (is_array($value)) {
            $id = array_key_exists('id', $value) ? trim((string) ($value['id'] ?? '')) : null;
            $en = array_key_exists('en', $value) ? trim((string) ($value['en'] ?? '')) : null;

            if ($id !== null || $en !== null) {
                return [
                    'id' => (string) ($id ?? ''),
                    'en' => (string) ($en ?? ''),
                ];
            }

            return null;
        }

        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        if ($trimmed === '' || ($trimmed[0] !== '{' && $trimmed[0] !== '[')) {
            return null;
        }

        $decoded = json_decode($trimmed, true);

        if (! is_array($decoded)) {
            return null;
        }

        $id = array_key_exists('id', $decoded) ? trim((string) ($decoded['id'] ?? '')) : null;
        $en = array_key_exists('en', $decoded) ? trim((string) ($decoded['en'] ?? '')) : null;

        if ($id === null && $en === null) {
            return null;
        }

        return [
            'id' => (string) ($id ?? ''),
            'en' => (string) ($en ?? ''),
        ];
    }
}
