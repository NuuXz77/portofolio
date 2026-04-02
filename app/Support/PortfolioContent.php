<?php

namespace App\Support;

use App\Models\SiteSetting;

class PortfolioContent
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = SiteSetting::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function set(string $key, mixed $value, string $type = 'json'): SiteSetting
    {
        return SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
