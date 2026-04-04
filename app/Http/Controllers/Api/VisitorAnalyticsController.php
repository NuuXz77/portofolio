<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VisitorAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitorAnalyticsController extends Controller
{
    public function __construct(private readonly VisitorAnalyticsService $analyticsService)
    {
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'uuid'],
        ]);

        $this->analyticsService->touchSession($validated['session_id']);

        return response()->json([
            'ok' => true,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function activeUsers(Request $request): JsonResponse
    {
        $activeUsers = $this->analyticsService->getActiveUsersCount(10);

        return response()->json([
            'active_users' => $activeUsers,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
