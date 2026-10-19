<?php

namespace App\Support\Workspace;

use App\Models\User;
use Illuminate\Http\Request;

final class SuperAdminWorkspaceMode
{
    public const SESSION_KEY = 'superadmin_workspace_mode';

    /** @return list<string> */
    public static function available(): array
    {
        return [
            'superadmin', 'participant', 'non_presenting_participant',
            'author', 'co_author', 'presenter', 'invited_speaker',
            'front_office', 'conference_admin', 'technical_admin',
            'finance', 'academic_committee', 'reviewer',
            'academic_decision_authority', 'event_operations',
            'session_chair', 'moderator', 'publication_team',
            'certificate_authority',
        ];
    }

    public static function current(Request $request): string
    {
        $actor = $request->user();

        if (! $actor instanceof User || ! $actor->isSuperAdmin()) {
            return 'participant';
        }

        $mode = $request->session()->get(self::SESSION_KEY);

        return is_string($mode) && in_array($mode, self::available(), true)
            ? $mode
            : 'superadmin';
    }

    public static function landing(string $mode): string
    {
        return match ($mode) {
            'superadmin' => 'admin.dashboard',
            'participant', 'non_presenting_participant', 'author', 'co_author', 'presenter' => 'dashboard',
            default => 'workspace.overview',
        };
    }
}
