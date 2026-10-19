<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Workspace\SuperAdminWorkspaceMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class SwitchSuperAdminWorkspaceController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);

        $data = $request->validate([
            'mode' => ['required', 'string', Rule::in(SuperAdminWorkspaceMode::available())],
        ]);

        $previous = SuperAdminWorkspaceMode::current($request);
        $next = $data['mode'];

        if ($previous !== $next) {
            $request->session()->put(SuperAdminWorkspaceMode::SESSION_KEY, $next);

            activity('security')
                ->causedBy($user)
                ->event('superadmin_workspace_view_changed')
                ->withProperties(['from' => $previous, 'to' => $next])
                ->log('Superadmin changed workspace view');
        }

        return to_route(SuperAdminWorkspaceMode::landing($next));
    }
}
