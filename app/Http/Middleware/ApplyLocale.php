<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

final class ApplyLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $cookieLocale = $request->cookie('locale');

        $configuredLocale = config('app.locale', Locale::English->value);

        $locale = Locale::tryFrom(
            is_string($cookieLocale) ? $cookieLocale : ''
        ) ?? Locale::tryFrom(
            is_string($configuredLocale) ? $configuredLocale : ''
        ) ?? Locale::English;

        App::setLocale($locale->value);

        return $next($request);
    }
}
