<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = (array) config('app.supported_locales', ['id', 'en']);
        $fallbackLocale = (string) config('app.fallback_locale', 'id');
        $routeLocale = trim((string) $request->route('locale', ''));

        if (! in_array($routeLocale, $supportedLocales, true)) {
            $routeLocale = $fallbackLocale;
        }

        app()->setLocale($routeLocale);
        $request->session()->put('locale', $routeLocale);
        URL::defaults(['locale' => $routeLocale]);

        return $next($request);
    }
}
