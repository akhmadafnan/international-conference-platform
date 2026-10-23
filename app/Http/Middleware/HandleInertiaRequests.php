<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use App\Models\ReviewAssignment;
use App\Models\User;
use App\Support\Academic\AcademicAccess;
use App\Support\Workspace\SuperAdminWorkspaceMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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
                'canAccessAdmin' => $request->user() instanceof User
                    && $request->user()->isSuperAdmin(),
                'workspaceMode' => SuperAdminWorkspaceMode::current($request),
                'workspaceOptions' => $request->user() instanceof User
                    && $request->user()->isSuperAdmin()
                        ? SuperAdminWorkspaceMode::available()
                        : [],
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
            'academicNavigation' => function () use ($request): array {
                $actor = $request->user();
                if (! $actor instanceof User || ! $actor->hasVerifiedEmail() || ! Schema::hasTable('academic_decisions')) {
                    return ['committee' => false, 'reviews' => false];
                }

                return ['committee' => app(AcademicAccess::class)->editions($actor) !== [], 'reviews' => ReviewAssignment::query()->where('reviewer_id', $actor->id)->exists()];
            },
        ];
    }
}
