<?php

namespace App\Http\Controllers\Submission;

use App\Actions\Submission\SubmitDraftAbstractAction;
use App\Actions\Submission\UpdateDraftSubmissionContributorsAction;
use App\Actions\Submission\UpdateDraftSubmissionReferencesAction;
use App\Actions\Submission\UploadDraftAbstractFileAction;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class AuthorSubmissionStagesController extends Controller
{
    public function contributors(
        Request $request,
        Submission $submission,
        UpdateDraftSubmissionContributorsAction $action,
    ): RedirectResponse {
        $actor = $this->owner($request, $submission);
        $this->draft($submission);

        $data = $request->validate([
            'contributors' => ['required', 'array', 'min:1', 'max:30'],
            'contributors.*' => ['required', 'array:id,display_name,given_name,family_name,single_name,email,country_code,orcid,is_corresponding,affiliations'],
            'contributors.*.id' => ['nullable', 'uuid'],
            'contributors.*.display_name' => ['required', 'string', 'max:255'],
            'contributors.*.given_name' => ['nullable', 'string', 'max:255'],
            'contributors.*.family_name' => ['nullable', 'string', 'max:255'],
            'contributors.*.single_name' => ['required', 'boolean'],
            'contributors.*.email' => ['required', 'email', 'max:255'],
            'contributors.*.country_code' => ['required', 'string', 'size:2'],
            'contributors.*.orcid' => ['nullable', 'string', 'max:255'],
            'contributors.*.is_corresponding' => ['required', 'boolean'],
            'contributors.*.affiliations' => ['required', 'array', 'min:1', 'max:10'],
            'contributors.*.affiliations.*' => ['required', 'array:institution_id,institution_name,subdivision,city,country_code'],
            'contributors.*.affiliations.*.institution_id' => ['nullable', 'uuid'],
            'contributors.*.affiliations.*.institution_name' => ['nullable', 'string', 'max:255'],
            'contributors.*.affiliations.*.subdivision' => ['nullable', 'string', 'max:255'],
            'contributors.*.affiliations.*.city' => ['nullable', 'string', 'max:255'],
            'contributors.*.affiliations.*.country_code' => ['nullable', 'string', 'size:2'],
        ]);

        try {
            $action->handle($actor, $submission, $data);
        } catch (DomainException) {
            throw ValidationException::withMessages(['contributors' => 'CONTRIBUTORS_INVALID']);
        }

        return to_route('submissions.details', $submission, 303);
    }

    public function references(
        Request $request,
        Submission $submission,
        UpdateDraftSubmissionReferencesAction $action,
    ): RedirectResponse {
        $actor = $this->owner($request, $submission);
        $this->draft($submission);
        $data = $request->validate([
            'references' => ['present', 'array', 'max:100'],
            'references.*' => ['required', 'array:id,raw_citation,doi,url'],
            'references.*.id' => ['nullable', 'uuid'],
            'references.*.raw_citation' => ['required', 'string', 'max:32000'],
            'references.*.doi' => ['nullable', 'string', 'max:255'],
            'references.*.url' => ['nullable', 'url', 'max:255'],
        ]);

        try {
            $action->handle($actor, $submission, $data);
        } catch (DomainException) {
            throw ValidationException::withMessages(['references' => 'REFERENCES_INVALID']);
        }

        return to_route('submissions.details', $submission, 303);
    }

    public function upload(
        Request $request,
        Submission $submission,
        UploadDraftAbstractFileAction $action,
    ): RedirectResponse {
        $actor = $this->owner($request, $submission);
        $this->draft($submission);
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        try {
            $action->handle($actor, $submission, $data['file']);
        } catch (DomainException) {
            throw ValidationException::withMessages(['file' => 'ABSTRACT_FILE_INVALID']);
        }

        return to_route('submissions.details', $submission, 303);
    }

    public function submit(
        Request $request,
        Submission $submission,
        SubmitDraftAbstractAction $action,
    ): RedirectResponse {
        $actor = $this->owner($request, $submission);
        $this->draft($submission);

        try {
            $action->handle($actor, $submission);
        } catch (DomainException) {
            throw ValidationException::withMessages([
                'submit' => 'SUBMISSION_NOT_READY_OR_WINDOW_CLOSED',
            ]);
        }

        return to_route('submissions.details', $submission, 303);
    }

    private function owner(Request $request, Submission $submission): User
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $owned = Submission::query()
            ->whereKey($submission->id)
            ->whereHas('registration.membership', fn ($query) => $query->where('user_id', $actor->id))
            ->exists();
        abort_unless($owned, 404);

        return $actor;
    }

    private function draft(Submission $submission): void
    {
        abort_unless($submission->academic_status === 'DRAFT', 409);
    }
}
