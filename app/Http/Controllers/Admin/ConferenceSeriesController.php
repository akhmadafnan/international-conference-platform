<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateConferenceSeriesAction;
use App\Actions\Admin\DeleteConferenceRecordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeleteConferenceRecordRequest;
use App\Http\Requests\Admin\StoreConferenceSeriesRequest;
use App\Models\ConferenceSeries;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class ConferenceSeriesController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeSuperAdmin($request);

        $series = ConferenceSeries::query()
            ->orderBy('code')
            ->paginate(20, ['id', 'code', 'name', 'acronym', 'status'])
            ->withQueryString();

        return Inertia::render('admin/series/Index', [
            'series' => [
                'data' => $series->items(),
                'total' => $series->total(),
                'from' => $series->firstItem(),
                'to' => $series->lastItem(),
                'perPage' => $series->perPage(),
                'currentPage' => $series->currentPage(),
                'lastPage' => $series->lastPage(),
                'firstUrl' => $series->onFirstPage() ? null : $series->url(1),
                'lastUrl' => $series->hasMorePages() ? $series->url($series->lastPage()) : null,
                'previousUrl' => $series->previousPageUrl(),
                'nextUrl' => $series->nextPageUrl(),
                'pages' => $series->onEachSide(1)->linkCollection()
                    ->slice(1, -1)
                    ->map(fn (array $link): array => [
                        'page' => is_numeric($link['label']) ? (int) $link['label'] : null,
                        'url' => $link['url'],
                    ])
                    ->values()
                    ->all(),
            ],
            'created' => (bool) $request->session()->get('series_created', false),
            'deleted' => (bool) $request->session()->get('series_deleted', false),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorizeSuperAdmin($request);

        return Inertia::render('admin/series/Create');
    }

    public function store(
        StoreConferenceSeriesRequest $request,
        CreateConferenceSeriesAction $action,
    ): RedirectResponse {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);

        try {
            $action->handle($actor, $request->validated());
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['code' => 'duplicate']);
        }

        return to_route('admin.series.index')->with('series_created', true);
    }

    public function destroy(DeleteConferenceRecordRequest $request, ConferenceSeries $series, DeleteConferenceRecordAction $action): RedirectResponse
    {
        $this->authorizeSuperAdmin($request);
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);

        try {
            $action->handle($actor, $series, (string) $request->validated('confirmation_code'));
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);
            throw ValidationException::withMessages(['delete' => 'failed']);
        }

        return to_route('admin.series.index')->with('series_deleted', true);
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        $actor = $request->user();
        abort_unless($actor instanceof User && $actor->isSuperAdmin(), 403);
    }
}
