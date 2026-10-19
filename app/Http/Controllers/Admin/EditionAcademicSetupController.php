<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WorkflowWindowCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEditionTrackRequest;
use App\Http\Requests\Admin\StoreEditionWindowRequest;
use App\Models\ConferenceEdition;
use App\Models\EditionWorkflowWindow;
use App\Models\Track;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class EditionAcademicSetupController extends Controller
{
    public function show(Request $request, ConferenceEdition $edition): Response
    {
        $this->superAdmin($request);

        return Inertia::render('admin/editions/AcademicSetup', [
            'edition' => [
                'id' => $edition->id,
                'code' => $edition->edition_code,
                'timezone' => $edition->timezone,
            ],
            'tracks' => $edition->tracks()->orderBy('code')->get()
                ->map(fn (Track $track): array => [
                    'id' => $track->id,
                    'code' => $track->code,
                    'name_i18n' => $track->name_i18n,
                    'active' => $track->active,
                ])->values()->all(),
            'windows' => $edition->workflowWindows()->orderBy('window_code')->get()
                ->map(fn (EditionWorkflowWindow $window): array => [
                    'code' => $window->window_code,
                    'opens_at' => $window->opens_at?->setTimezone($edition->timezone)->format('Y-m-d\TH:i'),
                    'closes_at' => $window->closes_at?->setTimezone($edition->timezone)->format('Y-m-d\TH:i'),
                    'active' => $window->active,
                ])->values()->all(),
            'allowedCodes' => array_map(
                fn (WorkflowWindowCode $code): string => $code->value,
                WorkflowWindowCode::cases(),
            ),
            'saved' => (bool) $request->session()->get('academic_setup_saved', false),
        ]);
    }

    public function storeTrack(
        StoreEditionTrackRequest $request,
        ConferenceEdition $edition,
    ): RedirectResponse {
        $actor = $this->superAdmin($request);
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));

        try {
            DB::transaction(function () use ($edition, $actor, $data): void {
                $track = $edition->tracks()->create($data);
                activity('academic')->causedBy($actor)->performedOn($track)
                    ->event('edition_track_created')
                    ->withProperties(['edition_id' => $edition->id, 'code' => $track->code])
                    ->log('Edition track configured');
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['code' => 'duplicate']);
        }

        return to_route('admin.editions.academic', $edition)->with('academic_setup_saved', true);
    }

    public function storeWindow(
        StoreEditionWindowRequest $request,
        ConferenceEdition $edition,
    ): RedirectResponse {
        $actor = $this->superAdmin($request);
        $data = $request->validated();
        try {
            $opens = $this->localDateTime($data['opens_at'], $edition->timezone);
            $closes = $this->localDateTime($data['closes_at'], $edition->timezone);
        } catch (Throwable) {
            throw ValidationException::withMessages(['opens_at' => 'invalid_local_time']);
        }
        if ($opens->greaterThanOrEqualTo($closes)) {
            throw ValidationException::withMessages(['closes_at' => 'end_after_start']);
        }

        DB::transaction(function () use ($edition, $actor, $data, $opens, $closes): void {
            $window = $edition->workflowWindows()->updateOrCreate(
                ['window_code' => $data['window_code']],
                [
                    'opens_at' => $opens->utc(),
                    'closes_at' => $closes->utc(),
                    'active' => (bool) $data['active'],
                ],
            );
            activity('academic')->causedBy($actor)->performedOn($window)
                ->event('edition_window_configured')
                ->withProperties([
                    'edition_id' => $edition->id,
                    'code' => $window->window_code,
                    'active' => $window->active,
                ])->log('Edition workflow window configured');
        });

        return to_route('admin.editions.academic', $edition)->with('academic_setup_saved', true);
    }

    private function localDateTime(string $value, string $timezone): CarbonImmutable
    {
        $date = CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $value, $timezone);
        if ($date === null || $date->format('Y-m-d\TH:i') !== $value) {
            throw new \InvalidArgumentException('Invalid local time');
        }

        return $date;
    }

    private function superAdmin(Request $request): User
    {
        $actor = $request->user();
        abort_unless($actor instanceof User && $actor->isSuperAdmin(), 403);

        return $actor;
    }
}
