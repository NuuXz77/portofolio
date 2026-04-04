<?php

use App\Http\Controllers\Api\VisitorAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::post('/heartbeat', [VisitorAnalyticsController::class, 'heartbeat'])
    ->withoutMiddleware('throttle:api')
    ->middleware('throttle:240,1');

Route::get('/active-users', [VisitorAnalyticsController::class, 'activeUsers'])
    ->withoutMiddleware('throttle:api')
    ->middleware('throttle:600,1');
