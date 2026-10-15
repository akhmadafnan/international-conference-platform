<?php

namespace App\Actions\Submission;

use App\Actions\Numbering\NextEditionNumberAction;
use App\Enums\Locale;
use App\Enums\RegistrationStatus;
use App\Models\ConferenceEdition;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class CreateDraftSubmissionAction
{
    public function __construct(
        private readonly NextEditionNumberAction $nextEditionNumber,
    ) {}

    public function handle(
        User $actor,
        ConferenceEdition $edition,
        Registration $registration,
        string $primaryLocale,
    ): Submission {
        return DB::transaction(function () use ($actor, $edition, $registration, $primaryLocale): Submission {
            $authoritativeRegistration = Registration::query()
                ->whereKey($registration->id)
                ->lockForUpdate()
                ->firstOrFail();

            $membership = $authoritativeRegistration->membership()->first();

            if (! $membership) {
                throw new DomainException(
                    'The Registration does not have an Edition participation context.',
                );
            }

            if ($membership->edition_id !== $edition->id) {
                throw new DomainException(
                    'The Registration does not belong to the supplied Conference Edition.',
                );
            }

            if ($membership->user_id !== $actor->id) {
                throw new DomainException(
                    'The Registration is not owned by the acting user.',
                );
            }

            if ($authoritativeRegistration->status === RegistrationStatus::CANCELLED) {
                throw new DomainException(
                    'A cancelled Registration is not a valid participation context.',
                );
            }

            $package = $authoritativeRegistration->package()->first();

            if (! $package || $package->edition_id !== $edition->id) {
                throw new DomainException(
                    'The Registration does not have a valid participation package context.',
                );
            }

            if (! in_array($primaryLocale, Locale::values(), true)) {
                throw new DomainException(
                    'The primary scholarly locale must use a supported locale.',
                );
            }

            $paperCode = $this->nextEditionNumber->handle(
                $edition,
                'PAPER',
                $edition->edition_code.'-P-',
            );

            $submission = Submission::query()->create([
                'edition_id' => $edition->id,
                'registration_id' => $authoritativeRegistration->id,
                'paper_code' => $paperCode,
                'track_id' => null,
                'primary_locale' => $primaryLocale,
                'academic_status' => 'DRAFT',
                'current_abstract_version' => 0,
            ]);

            activity('submission')
                ->causedBy($actor)
                ->performedOn($submission)
                ->event('submission_draft_created')
                ->withProperties([
                    'conference_edition_id' => $edition->id,
                    'registration_id' => $authoritativeRegistration->id,
                    'paper_code' => $paperCode,
                ])
                ->log('Draft submission created');

            return $submission->refresh();
        });
    }
}
