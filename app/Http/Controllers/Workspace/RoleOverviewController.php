<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Workspace\SuperAdminWorkspaceMode;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class RoleOverviewController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);

        return Inertia::render('workspace/RoleOverview', [
            'selectedMode' => SuperAdminWorkspaceMode::current($request),
        ]);
    }
}
