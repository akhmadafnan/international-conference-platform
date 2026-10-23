<?php

namespace App\Http\Controllers\Academic;

use App\Actions\Academic\ManageReviewerTopicEligibilityAction;
use App\Http\Controllers\Controller;
use App\Models\ConferenceEdition;
use App\Models\ReviewerTopicEligibility;
use App\Models\User;
use App\Support\Academic\AcademicAccess;
use App\Support\Academic\AcademicPagination;
use App\Support\Authorization\ActiveConferenceEditionContext;
use App\Support\Authorization\EditionScopedAuthorizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

final class ReviewerTopicEligibilityController extends Controller
{
    public function __construct(private readonly AcademicAccess $access, private readonly EditionScopedAuthorizer $authorizer) {}

    public function index(Request $request): Response
    {
        $actor = $this->actor($request);
        $editions = $this->access->editions($actor, 'review.assign');
        abort_if($editions === [], 403);
        abort_unless(Schema::hasTable('reviewer_topic_eligibilities'), 503, 'Reviewer Topic schema has not been installed.');
        $editionId = $request->session()->get(ActiveConferenceEditionContext::SESSION_KEY);
        $edition = in_array($editionId, array_column($editions, 'id'), true)
            ? ConferenceEdition::query()->findOrFail($editionId) : null;
        if ($edition !== null) {
            $this->authorizer->authorize($actor, 'review.assign', $edition->id);
        }
        $rows = ReviewerTopicEligibility::query()->where('edition_id', $edition?->id)
            ->with(['reviewer', 'track'])->orderByDesc('created_at')->orderBy('id')->paginate(30);

        return Inertia::render('academic/ReviewerTopics', [
            'editions' => $editions, 'editionId' => $edition?->id,
            'tracks' => $edition?->tracks()->where('active', true)->orderBy('code')->get(['id', 'code', 'name_i18n'])->toArray() ?? [],
            'eligibilities' => $rows->getCollection()->map(fn (ReviewerTopicEligibility $row): array => [
                'id' => $row->id, 'reviewer' => $row->reviewer->name, 'email' => $row->reviewer->email,
                'verified' => $row->reviewer->hasVerifiedEmail(), 'trackId' => $row->track_id,
                'trackCode' => $row->track->code, 'trackName' => $row->track->name_i18n,
                'trackActive' => $row->track->active, 'active' => $row->active,
            ])->all(),
            'pagination' => AcademicPagination::from($rows),
            'saved' => (bool) $request->session()->get('reviewer_topics_saved'),
        ]);
    }

    public function selectEdition(Request $request): RedirectResponse
    {
        $data = $request->validate(['edition_id' => 'required|uuid']);
        abort_unless(in_array($data['edition_id'], array_column($this->access->editions($this->actor($request), 'review.assign'), 'id'), true), 403);
        $request->session()->put(ActiveConferenceEditionContext::SESSION_KEY, $data['edition_id']);

        return to_route('academic.reviewer-topics.index', status: 303);
    }

    public function store(Request $request, ManageReviewerTopicEligibilityAction $manage): RedirectResponse
    {
        $data = $request->validate(['edition_id' => 'required|uuid']);
        $manage->grant($this->actor($request), ConferenceEdition::query()->whereKey($data['edition_id'])->firstOrFail(), $request->all());

        return to_route('academic.reviewer-topics.index', status: 303)->with('reviewer_topics_saved', true);
    }

    public function destroy(Request $request, ReviewerTopicEligibility $eligibility, ManageReviewerTopicEligibilityAction $manage): RedirectResponse
    {
        $manage->deactivate($this->actor($request), $eligibility);

        return to_route('academic.reviewer-topics.index', status: 303)->with('reviewer_topics_saved', true);
    }

    private function actor(Request $request): User
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $this->access->verified($actor);

        return $actor;
    }
}
