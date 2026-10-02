<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(
            ActiveConferenceEditionContext::class,
            fn (): ActiveConferenceEditionContext => new ActiveConferenceEditionContext,
        );
    }

    public function boot(): void
    {
        Gate::before(
            static fn (User $user, string $ability): ?bool => $user->isSuperAdmin()
                ? true
                : null,
        );
    }
}
