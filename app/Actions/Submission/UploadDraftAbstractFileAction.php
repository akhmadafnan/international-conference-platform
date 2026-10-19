<?php

namespace App\Actions\Submission;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use DomainException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use ZipArchive;

/**
 * Ingest a DRAFT abstract manuscript file for wizard Step 4 (Files) and keep an
 * immutable ABSTRACT_FILE version history on the protected private disk.
 */
class UploadDraftAbstractFileAction
{
    /**
     * Maximum accepted draft abstract file size: 10 MiB.
     */
    private const MAX_SIZE_BYTES = 10485760;

    /**
     * Canonical file role of the draft abstract manuscript file.
     */
    private const FILE_ROLE = 'ABSTRACT_FILE';

    private const DISK = 'private';

    private const STORAGE_DIRECTORY = 'submission-files/abstracts';

    private const VISIBILITY_CLASS = 'PRIVATE';

    private const ORIGINAL_NAME_MAX_BYTES = 255;

    /**
     * @var list<string>
     */
    private const ACCEPTED_EXTENSIONS = ['pdf', 'docx'];

    /**
     * Content sniffing results accepted for a verified PDF document.
     *
     * @var list<string>
     */
    private const PDF_MIME_TYPES = ['application/pdf', 'application/x-pdf'];

    /**
     * Content sniffing results accepted for a verified Word OOXML package.
     * Real DOCX packages are frequently reported as a generic ZIP archive.
     *
     * @var list<string>
     */
    private const DOCX_MIME_TYPES = [
        'application/zip',
        'application/x-zip',
        'application/x-zip-compressed',
        'application/octet-stream',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    private const DOCX_MIME_TYPE = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

    private const WORD_MAIN_CONTENT_TYPE = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml';

    private const WORD_DOCUMENT_NAMESPACE = 'schemas.openxmlformats.org/wordprocessingml/2006/main';

    /**
     * @var list<string>
     */
    private const REQUIRED_DOCX_ENTRIES = ['[Content_Types].xml', '_rels/.rels', 'word/document.xml'];

    private const MAX_ZIP_ENTRIES = 4096;

    /** Maximum uncompressed size of an inspected OOXML XML part. */
    private const MAX_XML_PART_BYTES = 1048576;

    private const MAX_TOTAL_UNCOMPRESSED_BYTES = 134217728;

    private const PDF_HEADER_BYTES = 1024;

    /**
     * Store a validated DRAFT abstract file and record the next immutable file version.
     *
     * Lock ordering inside the guarded transaction: submissions → registrations →
     * edition_memberships → participation_packages → submission_files. The authoritative Submission lock serializes version allocation.
     *
     * @throws DomainException when the upload content or the authoritative DRAFT ownership guards fail
     * @throws RuntimeException when protected storage, checksum or cleanup fails
     */
    public function handle(User $actor, Submission $submission, UploadedFile $file): SubmissionFile
    {
        $upload = $this->validateUpload($file);

        $this->assertAuthoritativeDraftAccess($actor, $submission);

        $disk = Storage::disk(self::DISK);
        $path = self::STORAGE_DIRECTORY.'/'.Str::uuid7().'.'.$upload['extension'];

        $stream = fopen($upload['real_path'], 'rb');

        if ($stream === false) {
            throw new RuntimeException('Unable to open the validated draft abstract file for protected storage.');
        }

        try {
            if (! $disk->put($path, $stream)) {
                throw new RuntimeException('Unable to store the draft abstract file in protected storage.');
            }

            return $this->persistAbstractFileVersion($actor, $submission, $upload, $path);
        } catch (Throwable $exception) {
            $this->removeAttemptedPath($disk, $path, $exception);

            throw $exception;
        } finally {
            fclose($stream);
        }
    }

    /**
     * Validate the untrusted upload without touching storage or the database.
     *
     * @return array{real_path: string, size: int, checksum: string, mime: string, extension: string, original_name: string}
     */
    private function validateUpload(UploadedFile $file): array
    {
        if (! $file->isValid()) {
            throw new DomainException('The draft abstract file upload did not complete successfully.');
        }

        $realPath = $file->getRealPath();

        if (! is_string($realPath) || $realPath === '' || ! is_readable($realPath)) {
            throw new DomainException('The uploaded draft abstract file cannot be read.');
        }

        $size = $file->getSize();

        if (! is_int($size) || $size < 1) {
            throw new DomainException('The draft abstract file must be a non-empty readable file.');
        }

        if ($size > self::MAX_SIZE_BYTES) {
            throw new DomainException('The draft abstract file exceeds the maximum allowed size of 10 MiB.');
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, self::ACCEPTED_EXTENSIONS, true)) {
            throw new DomainException('The draft abstract file must use a .pdf or .docx extension.');
        }

        $detectedMime = $this->detectMimeType($realPath);

        $this->assertDetectedMimeAcceptable($extension, $detectedMime);

        if ($extension === 'pdf') {
            $this->assertPdfContent($realPath);
            $mime = 'application/pdf';
        } else {
            $this->assertDocxPackage($realPath);
            $mime = self::DOCX_MIME_TYPE;
        }

        $checksum = hash_file('sha256', $realPath);

        if ($checksum === false) {
            throw new RuntimeException('Unable to calculate the draft abstract file checksum.');
        }

        return [
            'real_path' => $realPath,
            'size' => $size,
            'checksum' => $checksum,
            'mime' => $mime,
            'extension' => $extension,
            'original_name' => $this->sanitizeOriginalName($file->getClientOriginalName(), $extension),
        ];
    }

    /**
     * Read-only authoritative ownership precheck performed before any storage write.
     */
    private function assertAuthoritativeDraftAccess(User $actor, Submission $submission): void
    {
        $authoritative = Submission::query()->findOrFail($submission->id);

        if ($authoritative->academic_status !== 'DRAFT') {
            throw new DomainException(
                'Only a DRAFT Submission may receive a draft abstract file.',
            );
        }

        $registration = Registration::query()->findOrFail($authoritative->registration_id);

        $this->assertParticipationContext($authoritative, $registration, $actor);
    }

    /**
     * Guard the authoritative Registration, EditionMembership and package context.
     */
    private function assertParticipationContext(
        Submission $submission,
        Registration $registration,
        User $actor,
        bool $lockRelations = false,
    ): void {
        // The initial precheck is read-only. During the authoritative
        // transaction, lock both referenced rows so a concurrent edit cannot
        // change ownership/Edition after validation but before commit.
        $membershipQuery = $registration->membership();
        if ($lockRelations) {
            $membershipQuery->lockForUpdate();
        }

        $membership = $membershipQuery->first();

        if (! $membership) {
            throw new DomainException(
                'The Registration does not have an Edition participation context.',
            );
        }

        if ($membership->edition_id !== $submission->edition_id) {
            throw new DomainException(
                'The Registration does not belong to the Submission Conference Edition.',
            );
        }

        if ($membership->user_id !== $actor->id) {
            throw new DomainException(
                'The Submission is not owned by the acting user.',
            );
        }

        if ($registration->status === RegistrationStatus::CANCELLED) {
            throw new DomainException(
                'A cancelled Registration is not a valid participation context.',
            );
        }

        $packageQuery = $registration->package();
        if ($lockRelations) {
            $packageQuery->lockForUpdate();
        }

        $package = $packageQuery->first();

        if (! $package || $package->edition_id !== $submission->edition_id) {
            throw new DomainException(
                'The Registration does not have a valid participation package context.',
            );
        }
    }

    /**
     * Write both records inside one guarded transaction and supersede the prior version.
     *
     * @param  array{real_path: string, size: int, checksum: string, mime: string, extension: string, original_name: string}  $upload
     */
    private function persistAbstractFileVersion(User $actor, Submission $submission, array $upload, string $path): SubmissionFile
    {
        return DB::transaction(function () use ($actor, $submission, $upload, $path): SubmissionFile {
            $authoritative = Submission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($authoritative->academic_status !== 'DRAFT') {
                throw new DomainException(
                    'Only a DRAFT Submission may receive a draft abstract file.',
                );
            }

            $registration = Registration::query()
                ->whereKey($authoritative->registration_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->assertParticipationContext($authoritative, $registration, $actor, lockRelations: true);

            $existingFiles = SubmissionFile::query()
                ->where('submission_id', $authoritative->id)
                ->where('file_role', self::FILE_ROLE)
                ->orderBy('manuscript_version')
                ->lockForUpdate()
                ->get();

            $highestVersion = $existingFiles->max('manuscript_version');
            $nextVersion = $highestVersion === null ? 1 : ((int) $highestVersion) + 1;

            $activeFiles = $existingFiles
                ->filter(fn (SubmissionFile $file): bool => $file->status === 'ACTIVE')
                ->values();

            $predecessor = $activeFiles->last() ?? $existingFiles->last();

            $storedFile = StoredFile::query()->create([
                'disk' => self::DISK,
                'path' => $path,
                'original_name' => $upload['original_name'],
                'mime_type' => $upload['mime'],
                'size_bytes' => $upload['size'],
                'checksum_sha256' => $upload['checksum'],
                'visibility_class' => self::VISIBILITY_CLASS,
                'uploaded_by_user_id' => $actor->id,
            ]);

            $submissionFile = SubmissionFile::query()->create([
                'submission_id' => $authoritative->id,
                'stored_file_id' => $storedFile->id,
                'file_role' => self::FILE_ROLE,
                'manuscript_version' => $nextVersion,
                'uploaded_by_user_id' => $actor->id,
                'submitted_at' => now(),
                'supersedes_submission_file_id' => $predecessor?->id,
                'status' => 'ACTIVE',
            ]);

            foreach ($activeFiles as $activeFile) {
                SubmissionFile::query()
                    ->whereKey($activeFile->id)
                    ->update(['status' => 'SUPERSEDED']);
            }

            activity('submission')
                ->causedBy($actor)
                ->performedOn($authoritative)
                ->event('submission_abstract_file_uploaded')
                ->withProperties([
                    'conference_edition_id' => $authoritative->edition_id,
                    'registration_id' => $authoritative->registration_id,
                    'paper_code' => $authoritative->paper_code,
                    'file_role' => self::FILE_ROLE,
                    'submission_file_id' => $submissionFile->id,
                    'stored_file_id' => $storedFile->id,
                    'manuscript_version' => $nextVersion,
                    'supersedes_submission_file_id' => $predecessor?->id,
                    'is_replacement' => $predecessor !== null,
                ])
                ->log('Draft abstract file uploaded');

            return $submissionFile;
        });
    }

    /**
     * Remove only the newly attempted storage key and surface cleanup failures.
     *
     * @throws RuntimeException when the newly attempted storage key cannot be removed
     */
    private function removeAttemptedPath(Filesystem $disk, string $path, Throwable $failure): void
    {
        try {
            $deleted = $disk->delete($path);
            $stillPresent = $disk->exists($path);
        } catch (Throwable $cleanupFailure) {
            throw new RuntimeException(
                'The draft abstract file upload failed and the newly attempted storage path could not be removed: '.$cleanupFailure->getMessage(),
                0,
                $failure,
            );
        }

        if ($deleted === false || $stillPresent) {
            throw new RuntimeException(
                'The draft abstract file upload failed and the newly attempted storage path could not be removed.',
                0,
                $failure,
            );
        }
    }

    private function detectMimeType(string $realPath): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            throw new RuntimeException('Content based file type detection is unavailable.');
        }

        try {
            $mime = finfo_file($finfo, $realPath);
        } finally {
            finfo_close($finfo);
        }

        return is_string($mime) ? strtolower(trim($mime)) : '';
    }

    /**
     * A detected generic ZIP mime is acceptable for DOCX; the OOXML package
     * inspection remains the authoritative decision for that format.
     */
    private function assertDetectedMimeAcceptable(string $extension, string $detectedMime): void
    {
        $accepted = $extension === 'pdf' ? self::PDF_MIME_TYPES : self::DOCX_MIME_TYPES;

        if ($detectedMime === '' || ! in_array($detectedMime, $accepted, true)) {
            throw new DomainException(
                $extension === 'pdf'
                    ? 'The .pdf file content is not a recognized PDF document.'
                    : 'The .docx file content is not a recognized Office Open XML package.',
            );
        }
    }

    /**
     * Bounded structural screening, not a complete PDF interpreter.
     *
     * The last startxref must identify a real classic xref table or an xref
     * stream object. A document catalog must be linked by its trailer Root.
     * PDF rendering and content sanitization are separate downstream concerns.
     */
    private function assertPdfContent(string $realPath): void
    {
        $content = file_get_contents($realPath);

        if ($content === false) {
            throw new RuntimeException('Unable to read the uploaded PDF content.');
        }

        if (preg_match('/%PDF-(?:1\.[0-7]|2\.0)\b/', substr($content, 0, self::PDF_HEADER_BYTES)) !== 1) {
            throw new DomainException('The .pdf file does not carry a recognized PDF header.');
        }

        $tail = substr($content, -8192);
        if (preg_match_all('/startxref\s+([0-9]+)\s*%%EOF\b/', $tail, $markers, PREG_SET_ORDER) < 1) {
            throw new DomainException('The .pdf file lacks a valid final cross-reference pointer and EOF marker.');
        }

        $lastMarker = end($markers);
        if ($lastMarker === false) {
            throw new DomainException('The .pdf file does not contain a final cross-reference marker.');
        }

        $xrefOffset = (int) $lastMarker[1];
        if ($xrefOffset < 1 || $xrefOffset >= strlen($content)) {
            throw new DomainException('The .pdf cross-reference offset is outside the uploaded document.');
        }

        $xrefSection = substr($content, $xrefOffset);
        $catalogOffset = null;

        if (preg_match('/\Axref\b/', $xrefSection) === 1) {
            [$rootNumber, $rootGeneration, $catalogOffset] = $this->validateClassicPdfXref($content, $xrefSection);
        } else {
            // PDF 1.5+ may replace a classic xref table with an xref stream.
            if (preg_match('/\A\d+\s+\d+\s+obj\b(.*?)\bstream(?:\r\n|\r|\n)/s', $xrefSection, $stream) !== 1
                || preg_match('/\/Type\s*\/XRef\b/', $stream[1]) !== 1
                || preg_match('/\/W\s*\[\s*\d+\s+\d+\s+\d+\s*\]/', $stream[1]) !== 1
                || preg_match('/\/Size\s+\d+\b/', $stream[1]) !== 1
                || preg_match('/\bendstream\s+endobj\b/s', $xrefSection) !== 1) {
                throw new DomainException('The .pdf startxref does not point to a supported cross-reference structure.');
            }

            [$rootNumber, $rootGeneration] = $this->pdfCatalogReference($stream[1]);
        }

        $rootPattern = '/(?<!\d)'.preg_quote((string) $rootNumber, '/')
            .'\s+'.preg_quote((string) $rootGeneration, '/').'\s+obj\b(.*?)\bendobj\b/s';

        if (preg_match($rootPattern, $content, $catalog, PREG_OFFSET_CAPTURE) !== 1
            || preg_match('/\/Type\s*\/Catalog\b/', $catalog[1][0]) !== 1
            || preg_match('/\/Pages\s+\d+\s+\d+\s+R\b/', $catalog[1][0]) !== 1) {
            throw new DomainException('The .pdf Root does not reference a recognizable document catalog.');
        }

        if ($catalogOffset !== null && $catalog[0][1] !== $catalogOffset) {
            throw new DomainException('The .pdf document catalog does not match its cross-reference offset.');
        }
    }

    /**
     * @return array{int, int, int}
     */
    private function validateClassicPdfXref(string $content, string $xrefSection): array
    {
        if (preg_match('/\Axref\s+(.*?)\btrailer\s*<<(.*?)>>/s', $xrefSection, $parts) !== 1) {
            throw new DomainException('The .pdf has an invalid classic cross-reference table.');
        }

        [$rootNumber, $rootGeneration] = $this->pdfCatalogReference($parts[2]);

        $lines = preg_split('/\r\n|\r|\n/', trim($parts[1]));
        if ($lines === false) {
            throw new DomainException('The .pdf cross-reference entries are unreadable.');
        }

        $found = null;
        $hasInUseEntry = false;
        $cursor = 0;
        while ($cursor < count($lines)) {
            $header = trim($lines[$cursor++]);
            if (preg_match('/^(\d+)\s+(\d+)$/', $header, $section) !== 1) {
                throw new DomainException('The .pdf has an invalid cross-reference subsection.');
            }

            $firstObject = (int) $section[1];
            $count = (int) $section[2];
            if ($count < 1 || $count > 1000000 || $cursor + $count > count($lines)) {
                throw new DomainException('The .pdf has an invalid cross-reference entry count.');
            }

            for ($i = 0; $i < $count; $i++) {
                if (preg_match('/^(\d{10})\s+(\d{5})\s+([nf])$/', trim($lines[$cursor++]), $entry) !== 1) {
                    throw new DomainException('The .pdf has an invalid cross-reference entry.');
                }

                if ($entry[3] === 'n') {
                    $offset = (int) $entry[1];
                    if ($offset < 1 || $offset >= strlen($content)) {
                        throw new DomainException('The .pdf has an invalid in-use object offset.');
                    }

                    $hasInUseEntry = true;
                    if ($firstObject + $i === $rootNumber && (int) $entry[2] === $rootGeneration) {
                        $found = $offset;
                    }
                }
            }
        }

        if (! $hasInUseEntry || $found === null
            || preg_match('/\A'.preg_quote((string) $rootNumber, '/').'\s+'
                .preg_quote((string) $rootGeneration, '/').'\s+obj\b/', substr($content, $found, 80)) !== 1) {
            throw new DomainException('The .pdf trailer Root has no valid catalog cross-reference entry.');
        }

        return [$rootNumber, $rootGeneration, $found];
    }

    /**
     * @return array{int, int}
     */
    private function pdfCatalogReference(string $dictionary): array
    {
        if (preg_match('/\/Root\s+(\d+)\s+(\d+)\s+R\b/', $dictionary, $root) !== 1) {
            throw new DomainException('The .pdf cross-reference does not identify a document Root.');
        }

        return [(int) $root[1], (int) $root[2]];
    }

    private function assertDocxPackage(string $realPath): void
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('ZIP archive inspection support is unavailable.');
        }

        $zip = new ZipArchive;
        $opened = $zip->open($realPath);

        if ($opened !== true) {
            throw new DomainException('The .docx file is not a readable ZIP archive.');
        }

        try {
            $entryCount = $zip->numFiles;

            if ($entryCount < count(self::REQUIRED_DOCX_ENTRIES)) {
                throw new DomainException('The .docx file does not contain a complete Office Open XML package.');
            }

            if ($entryCount > self::MAX_ZIP_ENTRIES) {
                throw new DomainException('The .docx file contains an unsafe number of archive entries.');
            }

            /** @var array<string, int> $entrySizes */
            $entrySizes = [];
            $totalUncompressed = 0;

            for ($index = 0; $index < $entryCount; $index++) {
                $stat = $zip->statIndex($index);

                if ($stat === false) {
                    throw new DomainException('The .docx file contains an unreadable archive entry.');
                }

                $name = (string) $stat['name'];
                $entrySize = (int) $stat['size'];

                $this->assertSafeEntryName($name);

                if ((int) $stat['encryption_method'] !== ZipArchive::EM_NONE) {
                    throw new DomainException('The .docx file contains an encrypted archive entry.');
                }

                if (array_key_exists($name, $entrySizes)) {
                    throw new DomainException('The .docx file contains a duplicated archive entry.');
                }

                // ZIP media may legitimately exceed 1 MiB; only the three
                // XML documents parsed into memory have a 1 MiB part limit.
                if (in_array($name, self::REQUIRED_DOCX_ENTRIES, true)
                    && $entrySize > self::MAX_XML_PART_BYTES) {
                    throw new DomainException('The .docx file contains an oversized required XML part.');
                }

                $totalUncompressed += $entrySize;

                if ($totalUncompressed > self::MAX_TOTAL_UNCOMPRESSED_BYTES) {
                    throw new DomainException('The .docx file expands beyond the accepted archive size.');
                }

                if (stripos($name, 'vbaProject') !== false) {
                    throw new DomainException('The .docx file is a macro-enabled document and is not accepted.');
                }

                $entrySizes[$name] = $entrySize;
            }

            foreach (self::REQUIRED_DOCX_ENTRIES as $requiredEntry) {
                if (! array_key_exists($requiredEntry, $entrySizes)) {
                    throw new DomainException('The .docx file is not a complete Office Open XML Word document package.');
                }
            }

            $contentTypesXml = $this->readDocxEntry($zip, '[Content_Types].xml', $entrySizes['[Content_Types].xml']);

            if (stripos($contentTypesXml, 'macroEnabled') !== false) {
                throw new DomainException('The .docx file is a macro-enabled document and is not accepted.');
            }

            $contentTypes = $this->parseDocxXml($contentTypesXml, '[Content_Types].xml');
            $this->assertDocxXmlRoot($contentTypes, 'Types', 'http://schemas.openxmlformats.org/package/2006/content-types');
            $typeQuery = new \DOMXPath($contentTypes);
            $typeQuery->registerNamespace('ct', 'http://schemas.openxmlformats.org/package/2006/content-types');
            $wordOverrides = $typeQuery->query('/ct:Types/ct:Override[@PartName="/word/document.xml"]');

            $wordOverride = $wordOverrides === false ? null : $wordOverrides->item(0);
            if ($wordOverrides === false || $wordOverrides->length !== 1
                || ! $wordOverride instanceof \DOMElement
                || $wordOverride->getAttribute('ContentType') !== self::WORD_MAIN_CONTENT_TYPE) {
                throw new DomainException('The .docx file does not declare a valid Word document content type.');
            }

            $relationships = $this->parseDocxXml(
                $this->readDocxEntry($zip, '_rels/.rels', $entrySizes['_rels/.rels']),
                '_rels/.rels',
            );
            $this->assertDocxXmlRoot($relationships, 'Relationships', 'http://schemas.openxmlformats.org/package/2006/relationships');
            $relationQuery = new \DOMXPath($relationships);
            $relationQuery->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');
            $relationNodes = $relationQuery->query('/rel:Relationships/rel:Relationship');

            if ($relationNodes === false) {
                throw new DomainException('The .docx file does not contain valid package relationships.');
            }

            $officeRelationCount = 0;
            foreach ($relationNodes as $relation) {
                if (! $relation instanceof \DOMElement) {
                    continue;
                }

                if ($relation->getAttribute('Type') === 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument') {
                    $officeRelationCount++;
                    if ($relation->getAttribute('Target') !== 'word/document.xml'
                        || strcasecmp($relation->getAttribute('TargetMode'), 'External') === 0) {
                        throw new DomainException('The .docx officeDocument relationship does not target its local Word body.');
                    }
                }
            }

            if ($officeRelationCount !== 1) {
                throw new DomainException('The .docx file must contain exactly one local Word officeDocument relationship.');
            }

            $document = $this->parseDocxXml(
                $this->readDocxEntry($zip, 'word/document.xml', $entrySizes['word/document.xml']),
                'word/document.xml',
            );
            $this->assertDocxXmlRoot($document, 'document', 'http://'.self::WORD_DOCUMENT_NAMESPACE);
        } finally {
            $zip->close();
        }
    }

    private function readDocxEntry(ZipArchive $zip, string $name, int $expectedSize): string
    {
        $length = $expectedSize < 1 ? 1 : $expectedSize;

        $contents = $zip->getFromName($name, $length);

        if ($contents === false) {
            throw new DomainException('The .docx file contains an unreadable Office Open XML part.');
        }

        return $contents;
    }

    private function assertSafeEntryName(string $name): void
    {
        if ($name === '' || str_contains($name, "\0")) {
            throw new DomainException('The .docx file contains an unsafe archive entry name.');
        }

        if (str_starts_with($name, '/') || str_starts_with($name, '\\') || preg_match('#^[A-Za-z]:#', $name) === 1) {
            throw new DomainException('The .docx file contains an absolute archive entry path.');
        }

        if (str_contains($name, '\\')) {
            throw new DomainException('The .docx file contains an unsafe archive entry name.');
        }

        if (preg_match('#(^|/)\.\.(/|$)#', $name) === 1) {
            throw new DomainException('The .docx file contains a path traversal archive entry.');
        }
    }

    /**
     * Parse only bounded OOXML metadata/document parts, without DTDs, network
     * lookups, external entities, or any ZIP extraction to disk.
     */
    private function parseDocxXml(string $xml, string $part): \DOMDocument
    {
        if (strlen($xml) > self::MAX_XML_PART_BYTES
            || preg_match('/<!\s*(?:DOCTYPE|ENTITY)\b/i', $xml) === 1) {
            throw new DomainException('The .docx file contains an unsafe XML part: '.$part);
        }

        $previousErrors = libxml_use_internal_errors(true);
        try {
            $document = new \DOMDocument;
            $document->resolveExternals = false;
            $document->substituteEntities = false;

            if (! $document->loadXML($xml, LIBXML_NONET | LIBXML_COMPACT)
                || $document->doctype !== null
                || $document->documentElement === null) {
                throw new DomainException('The .docx file contains malformed XML in '.$part.'.');
            }

            return $document;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);
        }
    }

    private function assertDocxXmlRoot(\DOMDocument $xml, string $localName, string $namespace): void
    {
        $root = $xml->documentElement;

        if ($root === null || $root->localName !== $localName || $root->namespaceURI !== $namespace) {
            throw new DomainException('The .docx file contains an invalid Office Open XML root element.');
        }
    }

    /**
     * Reduce the client supplied filename to safe metadata; it is never used as a path.
     */
    private function sanitizeOriginalName(string $clientName, string $extension): string
    {
        $name = str_replace(['\\', '/'], '/', $clientName);

        $separator = strrpos($name, '/');

        if ($separator !== false) {
            $name = substr($name, $separator + 1);
        }

        $name = preg_replace('/[\x00-\x1F\x7F]/', '', $name) ?? '';
        $name = str_replace(['..', '<', '>', '"', '|', ':', '*', '?'], '', $name);
        $name = trim(trim($name), '.');

        if ($name === '') {
            $name = 'abstract';
        }

        if (strlen($name) > self::ORIGINAL_NAME_MAX_BYTES) {
            $suffix = '.'.$extension;
            $name = mb_strcut($name, 0, self::ORIGINAL_NAME_MAX_BYTES - strlen($suffix), 'UTF-8').$suffix;
        }

        return $name;
    }
}
