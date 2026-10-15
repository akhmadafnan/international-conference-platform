<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConferenceEdition;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);

        return Inertia::render('admin/Dashboard', [
            'editionCount' => ConferenceEdition::query()->count(),
            'recentEditions' => ConferenceEdition::query()
                ->with('series:id,name')
                ->latest('created_at')
                ->orderByDesc('id')
                ->limit(10)
                ->get(['id', 'series_id', 'edition_code', 'year', 'lifecycle_status'])
                ->map(fn (ConferenceEdition $edition): array => [
                    'id' => $edition->id,
                    'code' => $edition->edition_code,
                    'seriesName' => $edition->series?->name,
                    'year' => $edition->year,
                    'status' => $edition->lifecycle_status,
                ]),
        ]);
    }
}
