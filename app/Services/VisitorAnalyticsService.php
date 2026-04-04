<?php

namespace App\Services;

use App\Models\ActiveSession;

class VisitorAnalyticsService
{
    public function touchSession(string $sessionId): void
    {
        ActiveSession::query()->updateOrCreate(
            ['session_id' => $sessionId],
            ['last_active' => now()]
        );

        // Keep the table lean without running cleanup on every request.
        if (random_int(1, 40) === 1) {
            $this->cleanupInactiveSessions();
        }
    }

    public function getActiveUsersCount(int $activeWindowSeconds = 10): int
    {
        return ActiveSession::query()
            ->where('last_active', '>=', now()->subSeconds($activeWindowSeconds))
            ->count();
    }

    public function cleanupInactiveSessions(int $retentionHours = 24): int
    {
        return ActiveSession::query()
            ->where('last_active', '<', now()->subHours($retentionHours))
            ->delete();
    }
}
