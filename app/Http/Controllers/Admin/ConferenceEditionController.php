<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateConferenceEditionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreConferenceEditionRequest;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class ConferenceEditionController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeSuperAdmin($request);

        $editions = ConferenceEdition::query()
            ->with('series:id,code,name')
            ->latest('created_at')->orderByDesc('id')
            ->paginate(20, ['id', 'series_id', 'edition_code', 'edition_number', 'host_name', 'year', 'mode', 'timezone', 'starts_at', 'ends_at', 'lifecycle_status'])
            ->withQueryString();

        return Inertia::render('admin/editions/Index', [
            'editions' => [
                'data' => $editions->getCollection()->map(fn (ConferenceEdition $edition): array => [
                    'id' => $edition->id,
                    'code' => $edition->edition_code,
                    'number' => $edition->edition_number,
                    'hostName' => $edition->host_name,
                    'seriesName' => $edition->series?->name,
                    'seriesCode' => $edition->series?->code,
                    'year' => $edition->year,
                    'mode' => $edition->mode,
                    'timezone' => $edition->timezone,
                    'startsAt' => $edition->starts_at->utc()->toIso8601String(),
                    'endsAt' => $edition->ends_at->utc()->toIso8601String(),
                    'status' => $edition->lifecycle_status,
                ]),
                'total' => $editions->total(),
                'from' => $editions->firstItem(),
                'to' => $editions->lastItem(),
                'perPage' => $editions->perPage(),
                'currentPage' => $editions->currentPage(),
                'lastPage' => $editions->lastPage(),
                'firstUrl' => $editions->onFirstPage() ? null : $editions->url(1),
                'lastUrl' => $editions->hasMorePages() ? $editions->url($editions->lastPage()) : null,
                'previousUrl' => $editions->previousPageUrl(),
                'nextUrl' => $editions->nextPageUrl(),
                'pages' => $editions->onEachSide(1)->linkCollection()->slice(1, -1)
                    ->map(fn (array $link): array => [
                        'page' => is_numeric($link['label']) ? (int) $link['label'] : null,
                        'url' => $link['url'],
                    ])->values()->all(),
            ],
            'created' => (bool) $request->session()->get('edition_created', false),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorizeSuperAdmin($request);

        return Inertia::render('admin/editions/Create', [
            'series' => ConferenceSeries::query()->where('status', 'ACTIVE')->orderBy('code')->get(['id', 'code', 'name']),
        ]);
    }

    public function store(StoreConferenceEditionRequest $request, CreateConferenceEditionAction $action): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User && $actor->isSuperAdmin(), 403);

        try {
            $action->handle($actor, $request->validated());
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['edition_code' => 'duplicate']);
        }

        return to_route('admin.editions.index')->with('edition_created', true);
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        $actor = $request->user();
        abort_unless($actor instanceof User && $actor->isSuperAdmin(), 403);
    }
}
