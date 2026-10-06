<?php

namespace App\Actions\Registration;

use App\Enums\RegistrationStatus;
use App\Models\ConferenceEdition;
use App\Models\GeneratedDocument;
use App\Models\Registration;
use App\Models\User;
use App\Models\VerificationToken;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnsureEventPassAction
{
    public function handle(Registration $registration): GeneratedDocument
    {
        return DB::transaction(function () use ($registration): GeneratedDocument {
            $registration = Registration::query()
                ->lockForUpdate()
                ->findOrFail($registration->id);

            if ($registration->status !== RegistrationStatus::CONFIRMED) {
                throw new DomainException(
                    'Event Pass requires a confirmed registration.',
                );
            }

            $existing = $registration->generatedDocuments()
                ->where('document_type', GeneratedDocument::TYPE_EVENT_PASS)
                ->where('status', GeneratedDocument::STATUS_ISSUED)
                ->whereNull('revoked_at')
                ->latest('issued_at')
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            $membership = $registration->membership()
                ->with(['user', 'edition.series'])
                ->firstOrFail();

            $edition = $membership->edition;
            $user = $membership->user;

            if (
                ! $edition instanceof ConferenceEdition
                || ! $user instanceof User
            ) {
                throw new DomainException(
                    'Event Pass requires a valid Edition membership and participant.',
                );
            }

            $issuedAt = now();

            $document = $registration->generatedDocuments()->create([
                'edition_id' => $edition->id,
                'document_type' => GeneratedDocument::TYPE_EVENT_PASS,
                'recipient_user_id' => $user->id,
                'status' => GeneratedDocument::STATUS_ISSUED,
                'snapshot_json' => [
                    'registration_code' => $registration->registration_code,
                    'participant_name' => $user->name,
                    'package_name' => $registration->package_name_snapshot,
                    'participant_category' => $registration->participant_category,
                    'edition_code' => $edition->edition_code,
                    'edition_year' => $edition->year,
                    'conference_series' => $edition->series?->name,
                    'confirmed_at' => $registration->getRawOriginal('confirmed_at'),
                ],
                'issued_at' => $issuedAt,
            ]);

            $publicCode = $this->newPublicCode();

            $document->verificationTokens()->create([
                'purpose' => 'EVENT_PASS_LOOKUP',
                'token_hash' => hash('sha256', Str::random(64)),
                'public_code' => $publicCode,
                'active' => true,
            ]);

            activity('document')
                ->performedOn($document)
                ->event('event_pass_issued')
                ->withProperties([
                    'registration_id' => $registration->id,
                    'conference_edition_id' => $edition->id,
                    'lookup_public_code' => $publicCode,
                ])
                ->log('Event Pass issued');

            return $document->load('verificationTokens');
        });
    }

    private function newPublicCode(): string
    {
        do {
            $code = 'EP-'.Str::upper(Str::random(24));
        } while (
            VerificationToken::query()
                ->where('public_code', $code)
                ->exists()
        );

        return $code;
    }
}
