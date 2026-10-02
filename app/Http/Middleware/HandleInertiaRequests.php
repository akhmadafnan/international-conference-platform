<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @var string
     */
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = Locale::tryFrom(app()->getLocale())
            ?? Locale::English;

        return [
            ...parent::share($request),

            'name' => config('app.name'),

            'auth' => [
                'user' => $request->user(),
            ],

            'localization' => [
                'locale' => $locale->value,
                'direction' => $locale->direction(),

                'supportedLocales' => array_map(
                    static fn (Locale $supportedLocale): array => [
                        'code' => $supportedLocale->value,
                        'label' => $supportedLocale->nativeLabel(),
                        'direction' => $supportedLocale->direction(),
                    ],
                    Locale::cases(),
                ),
            ],

            'sidebarOpen' => ! $request->hasCookie('sidebar_state')
                || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
