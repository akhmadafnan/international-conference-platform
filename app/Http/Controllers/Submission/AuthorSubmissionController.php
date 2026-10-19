<?php

declare(strict_types=1);

namespace App\Http\Controllers\Submission;

use App\Actions\Submission\CreateDraftSubmissionAction;
use App\Actions\Submission\EvaluateDraftSubmissionReadinessAction;
use App\Actions\Submission\UpdateDraftSubmissionDetailsAction;
use App\Enums\RegistrationStatus;
use App\Http\Controllers\Controller;
use App\Models\ConferenceEdition;
use App\Models\ContributorAffiliation;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/** P03-I batch 01: authenticated author routes, never an admin override. */
final class AuthorSubmissionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $this->actor($request);

        $registrations = Registration::query()
            ->whereHas('membership', fn ($query) => $query->where('user_id', $user->id))
            ->with(['membership.edition', 'package'])
            ->latest('created_at')
            ->get()
            ->filter(static fn (Registration $registration): bool => $registration->status !== RegistrationStatus::CANCELLED
                && $registration->package !== null
                && $registration->membership?->edition !== null
                && $registration->package->edition_id === $registration->membership->edition_id
            )
            ->map(static fn (Registration $registration): array => [
                'id' => $registration->id,
                'code' => $registration->registration_code,
                'edition' => $registration->membership->edition->edition_code,
                'year' => $registration->membership->edition->year,
                'package' => $registration->package_name_snapshot,
            ])->values()->all();

        $submissions = Submission::query()
            ->whereHas('registration.membership', fn ($query) => $query->where('user_id', $user->id))
            ->with(['edition', 'primaryTranslation'])
            ->latest('created_at')
            ->get()
            ->map(static fn (Submission $submission): array => [
                'id' => $submission->id,
                'paperCode' => $submission->paper_code,
                'edition' => $submission->edition->edition_code,
                'title' => $submission->primaryTranslation?->title,
                'status' => $submission->academic_status,
                'detailsUrl' => route('submissions.details', $submission),
            ])->all();

        // Link only to real, existing edition registration routes; do not
        // label a package as currently available before the server checks it.
        $editionLinks = ConferenceEdition::query()
            ->whereHas('participationPackages', fn ($query) => $query->where('active', true))
            ->orderByDesc('year')->limit(20)->get()
            ->map(static fn (ConferenceEdition $edition): array => [
                'code' => $edition->edition_code,
                'year' => $edition->year,
                'registrationUrl' => route('editions.registration.show', $edition),
            ])->all();

        return Inertia::render('submissions/Index', [
            'registrations' => $registrations,
            'editionLinks' => $editionLinks,
            'submissions' => $submissions,
            'routes' => [
                'store' => route('submissions.store'),
                'dashboard' => route('dashboard'),
            ],
        ]);
    }

    public function store(Request $request, CreateDraftSubmissionAction $create): RedirectResponse
    {
        $user = $this->actor($request);
        $data = $request->validate([
            'registration_id' => ['required', 'uuid'],
            'primary_locale' => ['required', Rule::in(['id', 'en', 'ar'])],
        ]);

        $registration = Registration::query()
            ->whereKey($data['registration_id'])
            ->whereHas('membership', fn ($query) => $query->where('user_id', $user->id))
            ->with('membership.edition')
            ->firstOrFail();
        $edition = $registration->membership?->edition;
        abort_if($edition === null, 404);

        try {
            $submission = $create->handle($user, $edition, $registration, $data['primary_locale']);
        } catch (DomainException) {
            throw ValidationException::withMessages(['registration_id' => 'REGISTRATION_CONTEXT_INVALID']);
        }

        return to_route('submissions.details', $submission, 303);
    }

    public function details(Request $request, Submission $submission, EvaluateDraftSubmissionReadinessAction $readiness): Response
    {
        $submission = $this->owned($request, $submission);
        $submission->load(['edition', 'translations', 'keywords', 'contributors.affiliations', 'references', 'files.storedFile']);
        $locale = (string) $submission->getRawOriginal('primary_locale');

        $translation = $submission->translations->first(
            static fn ($row): bool => (string) $row->getRawOriginal('locale') === $locale,
        );
        $keywords = $submission->keywords
            ->filter(static fn ($row): bool => (string) $row->getRawOriginal('locale') === $locale)
            ->sortBy('sequence')
            ->pluck('value')
            ->implode("\n");

        $tracks = $submission->edition->tracks()
            ->where('active', true)->orderBy('code')->get()
            ->map(static fn ($track): array => [
                'id' => $track->id,
                'code' => $track->code,
                'name' => $track->name_i18n,
            ])->all();

        return Inertia::render('submissions/Details', [
            'submission' => [
                'id' => $submission->id,
                'paperCode' => $submission->paper_code,
                'edition' => $submission->edition->edition_code,
                'status' => $submission->academic_status,
                'primaryLocale' => $locale,
                'trackId' => $submission->track_id,
                'title' => $translation->title ?? '',
                'subtitle' => $translation->subtitle ?? '',
                'abstract' => $translation->abstract_text ?? '',
                'keywordsText' => $keywords,
            ],
            'tracks' => $tracks,
            'contributors' => $submission->contributors->map(fn (SubmissionContributor $item): array => [
                'id' => $item->id,
                'display_name' => $item->display_name,
                'given_name' => $item->given_name,
                'family_name' => $item->family_name,
                'single_name' => $item->single_name,
                'email' => $item->email,
                'country_code' => $item->country_code,
                'orcid' => $item->orcid_uri,
                'is_corresponding' => $item->is_corresponding,
                'affiliations' => $item->affiliations->map(fn (ContributorAffiliation $aff): array => [
                    'institution_id' => $aff->institution_id,
                    'institution_name' => $aff->institution_name_snapshot,
                    'subdivision' => $aff->subdivision_text,
                    'city' => $aff->city_text,
                    'country_code' => $aff->country_code,
                ])->all(),
            ])->all(),
            'references' => $submission->references->map(fn ($item): array => [
                'id' => $item->id,
                'raw_citation' => $item->raw_citation,
                'doi' => $item->doi,
                'url' => $item->url,
            ])->all(),
            'files' => $submission->files->where('file_role', 'ABSTRACT_FILE')->map(fn ($item): array => [
                'id' => $item->id,
                'version' => $item->manuscript_version,
                'name' => $item->storedFile?->original_name,
            ])->values()->all(),
            'readiness' => $submission->academic_status === 'DRAFT'
                ? $readiness->handle($this->actor($request), $submission)->toArray()
                : null,
            'routes' => [
                'index' => route('submissions.index'),
                'update' => route('submissions.details.update', $submission),
                'contributors' => route('submissions.contributors.update', $submission),
                'references' => route('submissions.references.update', $submission),
                'upload' => route('submissions.abstract-file.store', $submission),
                'submit' => route('submissions.submit', $submission),
            ],
        ]);
    }

    public function update(
        Request $request,
        Submission $submission,
        UpdateDraftSubmissionDetailsAction $update,
    ): RedirectResponse {
        $user = $this->actor($request);
        $submission = $this->owned($request, $submission);

        abort_unless($submission->academic_status === 'DRAFT', 409);

        $data = $request->validate([
            'track_id' => ['nullable', 'uuid'],
            'title' => ['required', 'string', 'max:500'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'abstract' => ['required', 'string', 'max:30000'],
            'keywords_text' => ['nullable', 'string', 'max:2000'],
        ]);

        $locale = (string) $submission->getRawOriginal('primary_locale');

        // The domain action is a complete replacement: preserve optional
        // secondary-language scholarly translations during Step 1 edits.
        $translations = [];
        foreach ($submission->translations as $translation) {
            $translations[(string) $translation->getRawOriginal('locale')] = [
                'title' => $translation->title,
                'subtitle' => $translation->subtitle,
                'abstract' => $translation->abstract_text,
            ];
        }
        $translations[$locale] = [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'abstract' => $data['abstract'],
        ];

        $keywords = [];
        foreach ($submission->keywords as $keyword) {
            $keywords[(string) $keyword->getRawOriginal('locale')][] = $keyword->value;
        }
        $lines = preg_split('/\R/u', (string) ($data['keywords_text'] ?? ''));
        $keywords[$locale] = array_values(array_filter(
            array_map('trim', $lines === false ? [] : $lines),
            static fn (string $value): bool => $value !== '',
        ));

        try {
            $update->handle($user, $submission, [
                'primary_locale' => $locale,
                'track_id' => $data['track_id'] ?? null,
                'translations' => $translations,
                'keywords' => $keywords,
            ]);
        } catch (DomainException) {
            throw ValidationException::withMessages(['details' => 'DRAFT_DETAILS_INVALID']);
        }

        return to_route('submissions.details', $submission, 303);
    }

    private function owned(Request $request, Submission $submission): Submission
    {
        return Submission::query()
            ->whereKey($submission->id)
            ->whereHas('registration.membership', fn ($query) => $query->where('user_id', $this->actor($request)->id))
            ->firstOrFail();
    }

    private function actor(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
