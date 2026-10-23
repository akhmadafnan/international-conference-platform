<?php

namespace App\Http\Controllers\Academic;

use App\Actions\Academic\AcademicWorkflow;
use App\Actions\Academic\CorrectSubmittedTopicRoutingAction;
use App\Http\Controllers\Controller;
use App\Models\AcademicDecision;
use App\Models\AcademicScreening;
use App\Models\GeneratedDocument;
use App\Models\ReviewAssignment;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\SubmissionSnapshot;
use App\Models\User;
use App\Support\Academic\AcademicAccess;
use App\Support\Academic\AcademicPagination;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class AcademicController extends Controller
{
    public function __construct(private readonly AcademicAccess $access, private readonly AcademicWorkflow $workflow) {}

    public function index(Request $request): Response
    {
        $actor = $this->actor($request);
        $editions = $this->access->editions($actor);
        abort_if($editions === [], 403);
        $this->installed();
        $editionId = $request->session()->get(ActiveConferenceEditionContext::SESSION_KEY);
        $allowed = array_column($editions, 'id');
        $rows = Submission::query()->whereIn('edition_id', in_array($editionId, $allowed, true) ? [$editionId] : [])
            ->where('academic_status', '!=', 'DRAFT')->orderByDesc('submitted_at')->paginate(20);

        return Inertia::render('academic/Index', [
            'editions' => $editions, 'editionId' => $editionId,
            'submissions' => $rows->getCollection()->map(fn (Submission $s) => ['id' => $s->id, 'paperCode' => $s->paper_code, 'status' => $s->academic_status, 'version' => $s->current_abstract_version, 'url' => route('academic.show', $s)])->all(),
            'pagination' => AcademicPagination::from($rows),
            'reviewerTopicsUrl' => $this->access->editions($actor, 'review.assign') !== [] ? route('academic.reviewer-topics.index') : null,
        ]);
    }

    public function selectEdition(Request $request): RedirectResponse
    {
        $data = $request->validate(['edition_id' => 'required|uuid']);
        abort_unless(in_array($data['edition_id'], array_column($this->access->editions($this->actor($request)), 'id'), true), 403);
        $request->session()->put(ActiveConferenceEditionContext::SESSION_KEY, $data['edition_id']);

        return to_route('academic.index', [], 303);
    }

    public function show(Request $request, Submission $submission): Response
    {
        $this->committee($request, $submission);
        $this->installed();
        abort_if($submission->academic_status === 'DRAFT', 404);
        $actor = $this->actor($request);
        $reportsAllowed = $actor->can('review.assign') || $actor->can('academic.decide');

        return Inertia::render('academic/Submission', [
            'paper' => $this->paper($submission),
            'screenings' => AcademicScreening::query()->where('submission_id', $submission->id)->orderBy('created_at')->get(['id', 'abstract_version', 'outcome', 'note'])->toArray(),
            'assignments' => ! $reportsAllowed ? [] : ReviewAssignment::query()->where('submission_id', $submission->id)->with(['reviewer', 'review', 'snapshot'])->get()->map(fn (ReviewAssignment $a) => [
                'id' => $a->id, 'reviewer' => $a->reviewer->name, 'status' => $a->status, 'version' => $a->snapshot->version,
                'sameAffiliation' => $a->same_affiliation, 'screeningNote' => $a->screening_note, 'responseNote' => $a->response_note,
                // Draft reports stay private to their reviewer until final submission.
                'review' => $a->review?->status === 'SUBMITTED' ? $a->review->only(['recommendation', 'author_comments', 'confidential_comments']) : null,
            ])->all(),
            'decisions' => ! $reportsAllowed ? [] : AcademicDecision::query()->where('submission_id', $submission->id)->orderBy('created_at')->get(['id', 'abstract_version', 'outcome', 'author_message', 'rationale'])->toArray(),
            'reviewerCount' => $this->workflow->reviewerCount($submission),
            'abilities' => ['screen' => $actor->can('submission.admin_screen'), 'assign' => $actor->can('review.assign'), 'decide' => $actor->can('academic.decide')],
            'saved' => (bool) $request->session()->get('academic_saved'),
            'routing' => [
                'currentTrackId' => $submission->track_id,
                'currentTopic' => $submission->track?->only(['code', 'name_i18n']),
                'snapshotTopic' => $this->workflow->snapshot($submission)->metadata_json['track'] ?? null,
                'canCorrect' => $actor->can('review.assign') && $submission->academic_status === 'SUBMITTED'
                    && $submission->edition->lifecycle_status !== 'ARCHIVED'
                    && ! ReviewAssignment::query()->where('submission_id', $submission->id)->exists(),
                'tracks' => $actor->can('review.assign') ? $submission->edition->tracks()->where('active', true)->orderBy('code')->get(['id', 'code', 'name_i18n'])->toArray() : [],
            ],
        ]);
    }

    public function correctTopic(Request $request, Submission $submission, CorrectSubmittedTopicRoutingAction $correct): RedirectResponse
    {
        $this->access->authorize($this->actor($request), 'review.assign', $submission);
        $data = $request->validate(['track_id' => 'required|uuid', 'reason' => 'required|string|max:2000']);
        $correct->handle($this->actor($request), $submission, $data['track_id'], $data['reason']);

        return back(303)->with('academic_saved', true);
    }

    public function screen(Request $request, Submission $submission): RedirectResponse
    {
        $this->workflow->screen($this->actor($request), $submission, $request->all());

        return back(303)->with('academic_saved', true);
    }

    public function assign(Request $request, Submission $submission): RedirectResponse
    {
        $this->workflow->assign($this->actor($request), $submission, $request->all());

        return back(303)->with('academic_saved', true);
    }

    public function cancel(Request $request, ReviewAssignment $assignment): RedirectResponse
    {
        $data = $request->validate(['reason' => 'required|string|max:8000']);
        $this->workflow->cancel($this->actor($request), $assignment, $data['reason']);

        return back(303)->with('academic_saved', true);
    }

    public function decide(Request $request, Submission $submission): RedirectResponse
    {
        $this->workflow->decide($this->actor($request), $submission, $request->all());

        return back(303)->with('academic_saved', true);
    }

    public function reviews(Request $request): Response
    {
        $this->installed();
        $actor = $this->actor($request);

        return Inertia::render('academic/Reviews', ['assignments' => ReviewAssignment::query()->where('reviewer_id', $actor->id)->with(['submission.edition', 'snapshot'])->latest()->get()->map(fn (ReviewAssignment $a) => [
            'id' => $a->id, 'paperCode' => $a->submission->paper_code, 'edition' => $a->submission->edition->edition_code, 'version' => $a->snapshot->version, 'status' => $a->status,
            'url' => in_array($a->status, ['INVITED', 'ACCEPTED', 'COMPLETED'], true) ? route('academic.review', $a) : null,
        ])->all()]);
    }

    public function review(Request $request, ReviewAssignment $assignment): Response
    {
        $this->workflow->reviewer($this->actor($request), $assignment, $assignment->submission);
        $snapshot = $assignment->snapshot;

        return Inertia::render('academic/Review', [
            'assignment' => ['id' => $assignment->id, 'status' => $assignment->status, 'version' => $snapshot->version, 'sameAffiliation' => $assignment->same_affiliation],
            'packet' => $this->packet($assignment->submission, $snapshot, route('academic.review.file', $assignment)),
            'review' => $assignment->review?->only(['status', 'recommendation', 'author_comments', 'confidential_comments']),
            'saved' => (bool) $request->session()->get('academic_saved'),
        ]);
    }

    public function respond(Request $request, ReviewAssignment $assignment): RedirectResponse
    {
        $data = $request->validate(['response' => 'required|string', 'note' => 'nullable|string|max:8000']);
        $this->workflow->respond($this->actor($request), $assignment, $data['response'], $data['note'] ?? '');

        return to_route('academic.reviews', [], 303)->with('academic_saved', true);
    }

    public function saveReview(Request $request, ReviewAssignment $assignment): RedirectResponse
    {
        $this->workflow->review($this->actor($request), $assignment, $request->all());

        return back(303)->with('academic_saved', true);
    }

    public function author(Request $request, Submission $submission): Response
    {
        $this->owner($request, $submission);
        $this->installed();
        abort_if($submission->academic_status === 'DRAFT', 404);
        $decisions = AcademicDecision::query()->where('submission_id', $submission->id)->orderBy('created_at')->get();
        $releasedVersions = $decisions->pluck('abstract_version');
        $reports = ReviewAssignment::query()->where('submission_id', $submission->id)->where('status', 'COMPLETED')
            ->whereHas('snapshot', fn ($q) => $q->whereIn('version', $releasedVersions))->with(['review', 'snapshot'])->get();
        $loa = $submission->generatedDocuments()->where('document_type', 'PRESENTATION_LOA')->where('status', 'ISSUED')->first();

        return Inertia::render('academic/Author', [
            'paper' => $this->paper($submission),
            'decisions' => $decisions->map(fn ($d) => $d->only(['abstract_version', 'outcome', 'author_message']))->all(),
            'screening' => $this->workflow->screening($submission)?->only(['outcome', 'note']),
            'reports' => $reports->filter(fn ($a) => $a->review?->status === 'SUBMITTED')->map(fn ($a) => ['version' => $a->snapshot->version, 'comments' => $a->review->author_comments])->values()->all(),
            'canRevise' => $submission->academic_status === 'REVISION_REQUIRED' || ($submission->academic_status === 'SUBMITTED' && $this->workflow->screening($submission)?->outcome === 'CORRECTION_REQUIRED'),
            'loaUrl' => $loa !== null && $submission->academic_status === 'ACCEPTED' ? route('academic.loa', $loa) : null,
            'saved' => (bool) $request->session()->get('academic_saved'),
        ]);
    }

    public function revise(Request $request, Submission $submission): RedirectResponse
    {
        $this->workflow->revise($this->actor($request), $submission, $request->all());

        return back(303)->with('academic_saved', true);
    }

    public function reviewFile(Request $request, ReviewAssignment $assignment): HttpResponse
    {
        $this->workflow->reviewer($this->actor($request), $assignment, $assignment->submission);

        return $this->snapshotFile($assignment->snapshot);
    }

    public function committeeFile(Request $request, Submission $submission): HttpResponse
    {
        $this->committee($request, $submission);

        return $this->snapshotFile($this->workflow->snapshot($submission));
    }

    public function authorFile(Request $request, Submission $submission): HttpResponse
    {
        $this->owner($request, $submission);

        return $this->snapshotFile($this->workflow->snapshot($submission));
    }

    public function loa(Request $request, GeneratedDocument $document): HttpResponse
    {
        $actor = $this->actor($request);
        abort_unless($document->document_type === 'PRESENTATION_LOA' && $document->status === 'ISSUED' && $document->recipient_user_id === $actor->id && $document->submission?->academic_status === 'ACCEPTED', 404);

        return $this->download($document->storedFile()->firstOrFail(), 'presentation-loa.pdf');
    }

    private function installed(): void
    {
        abort_unless(Schema::hasTable('academic_decisions'), 503, 'Academic schema has not been installed.');
    }

    private function actor(Request $request): User
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $this->access->verified($actor);

        return $actor;
    }

    private function owner(Request $request, Submission $s): void
    {
        abort_unless($s->registration()->whereHas('membership', fn ($q) => $q->where('user_id', $this->actor($request)->id))->exists(), 404);
    }

    private function committee(Request $request, Submission $s): void
    {
        $actor = $this->actor($request);
        foreach (['submission.admin_screen', 'review.assign', 'academic.decide'] as $ability) {
            if ($actor->can($ability)) {
                $this->access->authorize($actor, $ability, $s);
                $this->access->unconflicted($actor, $s);

                return;
            }
        }
        abort(403);
    }

    /** @return array<string, mixed> */
    private function paper(Submission $s): array
    {
        $fileRoute = request()->routeIs('academic.author') ? 'academic.author.file' : 'academic.file';

        return ['id' => $s->id, 'paperCode' => $s->paper_code, 'edition' => $s->edition->edition_code, 'status' => $s->academic_status, 'version' => $s->current_abstract_version, 'packet' => $this->packet($s, $this->workflow->snapshot($s), route($fileRoute, $s))];
    }

    /** @return array<string, mixed> */
    private function packet(Submission $s, SubmissionSnapshot $snapshot, string $fileUrl): array
    {
        $data = $snapshot->metadata_json;
        $text = [];
        foreach ($data['translations'] as $translation) {
            if ($translation['locale'] === (string) $s->getRawOriginal('primary_locale')) {
                $text = $translation;
                break;
            }
        }

        return ['paperCode' => $s->paper_code, 'edition' => $s->edition->edition_code, 'locale' => (string) $s->getRawOriginal('primary_locale'), 'title' => $text['title'] ?? '', 'abstract' => $text['abstract'] ?? '', 'keywords' => array_values(array_map(fn ($row) => $row['value'], array_filter($data['keywords'], fn ($row) => $row['locale'] === (string) $s->getRawOriginal('primary_locale')))), 'authors' => $data['contributors'], 'references' => $data['references'], 'fileUrl' => ($data['abstract_file'] ?? null) === null ? null : $fileUrl];
    }

    private function snapshotFile(SubmissionSnapshot $snapshot): HttpResponse
    {
        $id = $snapshot->metadata_json['abstract_file']['stored_file_id'] ?? null;
        abort_unless(is_string($id), 404);

        return $this->download(StoredFile::query()->findOrFail($id), 'abstract');
    }

    private function download(StoredFile $file, string $name): HttpResponse
    {
        abort_unless($file->disk === 'private' && $file->visibility_class === 'PRIVATE' && Storage::disk('private')->exists($file->path), 404);
        $bytes = Storage::disk('private')->get($file->path);
        abort_unless(hash_equals($file->checksum_sha256, hash('sha256', $bytes)), 409);
        $extension = $file->mime_type === 'application/pdf' ? '.pdf' : '.docx';

        return response($bytes, 200, ['Content-Type' => $file->mime_type, 'Content-Disposition' => 'attachment; filename="'.($name === 'abstract' ? $name.$extension : $name).'"', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
