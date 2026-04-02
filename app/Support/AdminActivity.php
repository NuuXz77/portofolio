<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class AdminActivity
{
    public static function log(string $action, string $module, ?string $description = null, array $metadata = []): void
    {
        ActivityLog::query()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'metadata' => $metadata ?: null,
        ]);
    }
}
