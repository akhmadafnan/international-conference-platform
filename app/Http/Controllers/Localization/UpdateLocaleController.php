<?php

namespace App\Http\Controllers\Localization;

use App\Enums\Locale;
use App\Http\Controllers\Controller;
use App\Http\Requests\Localization\UpdateLocaleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

final class UpdateLocaleController extends Controller
{
    public function __invoke(
        UpdateLocaleRequest $request,
    ): RedirectResponse {
        $locale = Locale::from(
            (string) $request->validated('locale')
        );

        App::setLocale($locale->value);

        return back(303)->withCookie(
            cookie(
                'locale',
                $locale->value,
                60 * 24 * 365,
            ),
        );
    }
}
