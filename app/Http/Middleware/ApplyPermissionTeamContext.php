<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class ApplyPermissionTeamContext
{
    public function __construct(
        private readonly ActiveConferenceEditionContext $editionContext,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->applyContext($request);

        try {
            return $next($request);
        } finally {
            setPermissionsTeamId(null);
            $this->editionContext->clear();
        }
    }

    private function applyContext(Request $request): void
    {
        $editionId = $request->session()->get(
            ActiveConferenceEditionContext::SESSION_KEY
        );

        if (! is_string($editionId)) {
            $this->clearContext();

            return;
        }

        try {
            $this->editionContext->activate($editionId);
        } catch (InvalidArgumentException) {
            $request->session()->forget(
                ActiveConferenceEditionContext::SESSION_KEY
            );

            $this->clearContext();

            return;
        }

        setPermissionsTeamId($this->editionContext->id());

        $user = $request->user();

        if ($user instanceof User) {
            $user->unsetRelation('roles')
                ->unsetRelation('permissions');
        }
    }

    private function clearContext(): void
    {
        $this->editionContext->clear();

        setPermissionsTeamId(null);
    }
}
