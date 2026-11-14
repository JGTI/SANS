<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['es', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $queryLocale = strtolower((string) $request->query('lang', ''));

        if (in_array($queryLocale, self::SUPPORTED_LOCALES, true)) {
            $locale = $queryLocale;
            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
            Cookie::queue('spacevision_locale', $locale, 60 * 24 * 365);
        } elseif ($request->hasSession() && in_array($request->session()->get('locale'), self::SUPPORTED_LOCALES, true)) {
            $locale = $request->session()->get('locale');
        } elseif (in_array($request->cookie('spacevision_locale'), self::SUPPORTED_LOCALES, true)) {
            $locale = $request->cookie('spacevision_locale');
        } else {
            $locale = 'es';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
