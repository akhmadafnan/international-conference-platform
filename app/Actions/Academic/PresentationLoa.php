<?php

declare(strict_types=1);

namespace App\Actions\Academic;

use App\Actions\Numbering\NextEditionNumberAction;
use App\Models\AcademicDecision;
use App\Models\ConferenceEdition;
use App\Models\GeneratedDocument;
use App\Models\NumberSequence;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use RuntimeException;

final class PresentationLoa
{
    /** Called only inside the locked decision transaction. Compensate file on rollback.
     * @param-out string $written
     */
    public function issue(User $actor, Submission $s, AcademicDecision $decision, ?string &$written): GeneratedDocument
    {
        if ($s->academic_status !== 'ACCEPTED' || $decision->outcome !== 'ACCEPTED') {
            throw new RuntimeException('Acceptance is required.');
        }
        $edition = ConferenceEdition::query()->whereKey($s->edition_id)->lockForUpdate()->firstOrFail();
        $snapshot = $s->snapshots()->where('version', $s->current_abstract_version)->whereIn('snapshot_type', ['INITIAL_SUBMISSION', 'ABSTRACT_REVISION', 'ADMIN_CORRECTION'])->firstOrFail();
        $metadata = $snapshot->metadata_json;
        $locale = (string) $s->getRawOriginal('primary_locale');
        $text = [];
        foreach ($metadata['translations'] as $translation) {
            if ($translation['locale'] === $locale) {
                $text = $translation;
                break;
            }
        }
        $sequence = NumberSequence::query()->where('edition_id', $edition->id)->where('sequence_type', 'PRESENTATION_LOA')->first();
        $padding = $sequence->configuration_json['padding'] ?? 4;
        if (! is_int($padding) || $padding < 1 || $padding > 255) {
            throw new RuntimeException('Invalid Presentation LoA numbering configuration.');
        }
        $number = app(NextEditionNumberAction::class)->handle($edition, 'PRESENTATION_LOA', $sequence->prefix ?? $edition->edition_code.'/LOA/', $padding);
        $issuedAt = now();
        $evidence = [
            'schema' => 'iches.presentation_loa.v1', 'decision_id' => $decision->id,
            'snapshot_id' => $snapshot->id, 'snapshot_checksum' => $snapshot->checksum,
            'issuer_id' => $actor->id, 'edition' => $edition->edition_code,
            'host' => $edition->host_name, 'paper_code' => $s->paper_code,
            'title' => $text['title'] ?? '', 'authors' => array_column($metadata['contributors'], 'display_name'),
            'outcome' => 'ACCEPTED', 'number' => $number, 'locale' => $locale,
            'date' => $issuedAt->setTimezone($edition->timezone)->format('Y-m-d'), 'timezone' => $edition->timezone,
            'issued_at' => $issuedAt->toIso8601String(),
        ];
        $pdf = new Mpdf(['tempDir' => storage_path('framework/cache/data/mpdf'), 'default_font' => 'dejavusans', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $pdf->SetTitle('Presentation LoA — '.$s->paper_code);
        $pdf->SetCreator('ICHES');
        $pdf->SetAuthor((string) $edition->host_name);
        $pdf->WriteHTML(view('documents.presentation-loa', ['document' => $evidence])->render());
        $bytes = $pdf->Output('', Destination::STRING_RETURN);
        $written = 'academic/loa/'.Str::uuid7().'.pdf';
        if (! Storage::disk('private')->put($written, $bytes)) {
            throw new RuntimeException('Cannot store Presentation LoA.');
        }
        $file = StoredFile::query()->create(['disk' => 'private', 'path' => $written, 'original_name' => 'presentation-loa.pdf', 'mime_type' => 'application/pdf', 'size_bytes' => strlen($bytes), 'checksum_sha256' => hash('sha256', $bytes), 'visibility_class' => 'PRIVATE', 'uploaded_by_user_id' => $actor->id]);
        $owner = $s->registration->membership->user_id;
        $doc = GeneratedDocument::query()->create(['edition_id' => $s->edition_id, 'submission_id' => $s->id, 'registration_id' => $s->registration_id, 'recipient_user_id' => $owner, 'document_type' => 'PRESENTATION_LOA', 'status' => 'ISSUED', 'document_number' => $number, 'snapshot_json' => $evidence, 'stored_file_id' => $file->id, 'issued_at' => $issuedAt]);
        activity('academic')->causedBy($actor)->performedOn($doc)->event('presentation_loa_issued')->withProperties(['conference_edition_id' => $s->edition_id, 'decision_id' => $decision->id, 'snapshot_id' => $snapshot->id, 'sha256' => $file->checksum_sha256])->log('Presentation LoA issued');

        return $doc;
    }
}
