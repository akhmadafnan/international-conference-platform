<?php

namespace App\Support\Academic;

use App\Models\ConferenceEdition;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\User;
use App\Support\Authorization\EditionScopedAuthorizer;
use Illuminate\Auth\Access\AuthorizationException;

final class AcademicAccess
{
    public function __construct(private readonly EditionScopedAuthorizer $authorizer) {}

    public function authorize(User $actor, string $ability, Submission $submission): void
    {
        $this->verified($actor);
        $this->authorizer->authorize($actor, $ability, $submission->edition_id);
    }

    public function verified(User $actor): void
    {
        if (! $actor->hasVerifiedEmail()) {
            throw new AuthorizationException;
        }
    }

    /** @return list<array{id: string, code: string}> */
    public function editions(User $actor, ?string $ability = null): array
    {
        $previous = getPermissionsTeamId();
        $result = [];
        try {
            foreach (ConferenceEdition::query()->orderBy('edition_code')->get() as $edition) {
                setPermissionsTeamId($edition->id);
                $actor->unsetRelation('roles')->unsetRelation('permissions');
                $abilities = $ability === null ? ['submission.admin_screen', 'review.assign', 'academic.decide'] : [$ability];
                if (array_filter($abilities, fn (string $candidate): bool => $actor->can($candidate)) !== []) {
                    $result[] = ['id' => $edition->id, 'code' => $edition->edition_code];
                }
            }
        } finally {
            setPermissionsTeamId($previous);
            $actor->unsetRelation('roles')->unsetRelation('permissions');
        }

        return $result;
    }

    public function isAuthor(User $actor, Submission $submission): bool
    {
        return $submission->registration()->whereHas('membership', fn ($q) => $q->where('user_id', $actor->id))->exists()
            || $submission->contributors()->where(fn ($q) => $q->where('linked_user_id', $actor->id)->orWhereRaw('LOWER(email) = ?', [mb_strtolower($actor->email)]))->exists();
    }

    public function unconflicted(User $actor, Submission $submission): void
    {
        if ($this->isAuthor($actor, $submission)
            || ReviewAssignment::query()->where('submission_id', $submission->id)->where('reviewer_id', $actor->id)->where('status', 'COI')->exists()) {
            throw new AuthorizationException('A conflict of interest prevents access to this academic resource.');
        }
    }

    public function sameAffiliation(User $reviewer, Submission $submission): bool
    {
        $known = SubmissionContributor::query()->where(fn ($q) => $q->where('linked_user_id', $reviewer->id)->orWhereRaw('LOWER(email) = ?', [mb_strtolower($reviewer->email)]))
            ->with('affiliations')->get()->flatMap(fn ($c) => $c->affiliations);
        foreach ($submission->contributors()->with('affiliations')->get() as $author) {
            foreach ($author->affiliations as $aff) {
                foreach ($known as $other) {
                    if (($aff->institution_id !== null && $aff->institution_id === $other->institution_id)
                        || ($aff->ror_uri_snapshot !== null && $aff->ror_uri_snapshot === $other->ror_uri_snapshot)
                        || (trim((string) $aff->institution_name_snapshot) !== '' && mb_strtolower(trim($aff->institution_name_snapshot)) === mb_strtolower(trim($other->institution_name_snapshot)))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
