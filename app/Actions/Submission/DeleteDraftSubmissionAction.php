<?php

namespace App\Actions\Submission;

use App\Models\Registration;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final class DeleteDraftSubmissionAction
{
    public function handle(User $actor, Submission $submission): void
    {
        DB::transaction(function () use ($actor, $submission): void {
            // Match the existing submit/edit/upload lock order.
            $draft = Submission::query()->whereKey($submission->id)->lockForUpdate()->firstOrFail();
            $registration = Registration::query()->whereKey($draft->registration_id)->lockForUpdate()->firstOrFail();
            $membership = $registration->membership()->lockForUpdate()->firstOrFail();

            if ($membership->user_id !== $actor->id || $membership->edition_id !== $draft->edition_id) {
                throw new DomainException('SUBMISSION_NOT_OWNED');
            }
            if ($draft->academic_status !== 'DRAFT') {
                throw new DomainException('SUBMISSION_NOT_DRAFT');
            }
            if ($draft->current_abstract_version !== 0 || $draft->submitted_at !== null
                || $draft->actual_presenter_contributor_id !== null
                || $draft->accepted_at !== null || $draft->rejected_at !== null
                || $draft->final_academic_approved_at !== null || $draft->snapshots()->exists()
                || $draft->generatedDocuments()->exists()) {
                throw new DomainException('SUBMISSION_HAS_ACADEMIC_EVIDENCE');
            }
            foreach (['academic_screenings', 'review_assignments', 'academic_decisions'] as $table) {
                if (DB::table($table)->where('submission_id', $draft->id)->exists()) {
                    throw new DomainException('SUBMISSION_HAS_ACADEMIC_EVIDENCE');
                }
            }

            $files = $draft->files()->lockForUpdate()->get();
            if ($files->contains(fn ($file): bool => $file->file_role !== 'ABSTRACT_FILE')
                || DB::table('submission_files')->whereNot('submission_id', $draft->id)
                    ->whereIn('supersedes_submission_file_id', $files->modelKeys())->exists()) {
                throw new DomainException('SUBMISSION_HAS_ACADEMIC_EVIDENCE');
            }
            $storedFiles = StoredFile::query()->whereIn('id', $files->pluck('stored_file_id'))
                ->orderBy('id')->lockForUpdate()->get();

            activity('submission')->causedBy($actor)->performedOn($draft)
                ->event('submission_draft_deleted')->withProperties([
                    'conference_edition_id' => $draft->edition_id,
                    'registration_id' => $registration->id,
                    'paper_code' => $draft->paper_code,
                    'stored_file_ids' => $storedFiles->modelKeys(),
                ])->log('Draft submission deleted');

            // Cascades remove translations, keywords, contributors/affiliations,
            // references and file versions. Evidence was explicitly refused above.
            $draft->delete();

            foreach ($storedFiles as $stored) {
                if ($this->isReferenced($stored->id) || $stored->disk !== 'private'
                    || $stored->visibility_class !== 'PRIVATE' || $stored->uploaded_by_user_id !== $actor->id
                    || preg_match('~\Asubmission-files/abstracts/[a-zA-Z0-9-]+\.(pdf|docx)\z~', $stored->path) !== 1) {
                    continue;
                }
                // Removing the unreferenced row prevents new FK references from
                // being attached before post-commit physical cleanup.
                $stored->delete();
                DB::afterCommit(static function () use ($stored): void {
                    try {
                        if (StoredFile::query()->where('path', $stored->path)->exists()) {
                            return;
                        }
                        $disk = Storage::disk('private');
                        if (! $disk->delete($stored->path)) {
                            throw new RuntimeException('Draft file cleanup failed after database commit.');
                        }
                    } catch (Throwable $exception) {
                        activity('submission')->event('submission_draft_file_cleanup_failed')
                            ->withProperties(['stored_file_id' => $stored->id, 'path' => $stored->path])
                            ->log('Draft deleted; private file cleanup requires retry');
                        report($exception);
                    }
                });
            }
        }, 3);
    }

    private function isReferenced(string $id): bool
    {
        foreach ([
            'submission_files' => 'stored_file_id',
            'conference_series' => 'logo_file_id',
            'payment_proofs' => 'stored_file_id',
            'refunds' => 'proof_file_id',
            'generated_documents' => 'stored_file_id',
        ] as $table => $column) {
            if (DB::table($table)->where($column, $id)->exists()) {
                return true;
            }
        }

        return false;
    }
}
