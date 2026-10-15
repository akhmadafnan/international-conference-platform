<?php

use App\Actions\Submission\UploadDraftAbstractFileAction;
use App\Enums\BillingMode;
use App\Enums\Locale;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\StoredFile;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionSnapshot;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert;

function draftAbstractFileEdition(string $editionCode = 'ICHES27'): ConferenceEdition
{
    $series = ConferenceSeries::query()->firstOrCreate(
        ['code' => 'ICHES'],
        [
            'name' => 'International Conference on Humanity Education and Society',
            'status' => 'ACTIVE',
        ],
    );

    return ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => $editionCode,
        'edition_number' => 6,
        'year' => 2027,
        'host_name' => 'Universitas Islam Syarifuddin Lumajang',
        'mode' => 'OFFLINE',
        'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDay(),
        'lifecycle_status' => 'DRAFT',
    ]);
}

function draftAbstractFilePackage(ConferenceEdition $edition, bool $active = true): ParticipationPackage
{
    return ParticipationPackage::query()->create([
        'edition_id' => $edition->id,
        'code' => 'PRESENTER',
        'name_i18n' => ['id' => 'Presenter', 'en' => 'Presenter'],
        'billing_mode' => BillingMode::PAID->value,
        'price' => '750000.00',
        'currency_code' => 'IDR',
        'active' => $active,
        'display_order' => 1,
    ]);
}

/** @return array{user: User, membership: EditionMembership, registration: Registration} */
function draftAbstractFileRegistration(
    ConferenceEdition $edition,
    ParticipationPackage $package,
    string $status = 'PENDING',
): array {
    $user = User::factory()->create();

    $membership = EditionMembership::query()->create([
        'edition_id' => $edition->id,
        'user_id' => $user->id,
        'membership_status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $registration = Registration::query()->create([
        'membership_id' => $membership->id,
        'registration_code' => 'REG-'.Str::uuid()->toString(),
        'package_id' => $package->id,
        'package_name_snapshot' => 'Presenter',
        'billing_mode' => BillingMode::PAID->value,
        'fee_amount' => '750000.00',
        'currency_code' => 'IDR',
        'status' => $status,
    ]);

    return compact('user', 'membership', 'registration');
}

/** @param array<string, mixed> $overrides */
function draftAbstractFileSubmission(
    ConferenceEdition $edition,
    Registration $registration,
    array $overrides = [],
): Submission {
    return Submission::query()->create(array_merge([
        'edition_id' => $edition->id,
        'registration_id' => $registration->id,
        'paper_code' => 'ICHES27-P-0001',
        'track_id' => null,
        'primary_locale' => Locale::Indonesian->value,
        'academic_status' => 'DRAFT',
        'current_abstract_version' => 0,
    ], $overrides));
}

/**
 * @param  array<string, mixed>  $submissionOverrides
 * @return array{edition: ConferenceEdition, package: ParticipationPackage, user: User, membership: EditionMembership, registration: Registration, submission: Submission}
 */
function draftAbstractFileContext(array $submissionOverrides = []): array
{
    $edition = draftAbstractFileEdition();
    $package = draftAbstractFilePackage($edition);

    ['user' => $user, 'membership' => $membership, 'registration' => $registration] = draftAbstractFileRegistration(
        $edition,
        $package,
    );

    $submission = draftAbstractFileSubmission($edition, $registration, $submissionOverrides);

    return compact('edition', 'package', 'user', 'membership', 'registration', 'submission');
}

function draftAbstractFilePdf(string $body = 'Draft abstract body'): string
{
    $stream = 'BT /F1 12 Tf 72 720 Td ('.addcslashes($body, '\\()').') Tj ET';

    $objects = [
        1 => '<< /Type /Catalog /Pages 2 0 R >>',
        2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
        3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
        4 => '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream",
        5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
    ];

    $pdf = "%PDF-1.4\n";
    $offsets = [];

    foreach ($objects as $number => $definition) {
        $offsets[$number] = strlen($pdf);
        $pdf .= $number." 0 obj\n".$definition."\nendobj\n";
    }

    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 6\n0000000000 65535 f \n";

    foreach ($offsets as $offset) {
        $pdf .= sprintf('%010d 00000 n '."\n", $offset);
    }

    $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF\n";

    return $pdf;
}

function draftAbstractFileContentTypes(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
        .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        .'<Default Extension="xml" ContentType="application/xml"/>'
        .'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
        .'</Types>';
}

function draftAbstractFileRelationships(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
        .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
        .'</Relationships>';
}

function draftAbstractFileDocument(string $body = 'Draft abstract body'): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
        .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        .'<w:body><w:p><w:r><w:t>'.$body.'</w:t></w:r></w:p></w:body>'
        .'</w:document>';
}

/**
 * @param  array{entries?: array<string, string>, omit?: list<string>}  $overrides
 */
function draftAbstractFileDocx(array $overrides = []): string
{
    $entries = array_merge([
        '[Content_Types].xml' => draftAbstractFileContentTypes(),
        '_rels/.rels' => draftAbstractFileRelationships(),
        'word/document.xml' => draftAbstractFileDocument(),
    ], $overrides['entries'] ?? []);

    foreach ($overrides['omit'] ?? [] as $entryName) {
        unset($entries[$entryName]);
    }

    $path = sys_get_temp_dir().'/draft-abstract-fixture-'.bin2hex(random_bytes(8)).'.docx';

    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    foreach ($entries as $entryName => $contents) {
        $zip->addFromString($entryName, $contents);
    }

    $zip->close();

    $bytes = (string) file_get_contents($path);

    unlink($path);

    return $bytes;
}

function draftAbstractFileUpload(
    string $content,
    string $clientName,
    ?string $clientMime = null,
    ?int $error = null,
): UploadedFile {
    $path = sys_get_temp_dir().'/draft-abstract-upload-'.bin2hex(random_bytes(8));
    file_put_contents($path, $content);

    return new UploadedFile($path, $clientName, $clientMime ?? 'application/octet-stream', $error, true);
}

function draftAbstractFileAction(): UploadDraftAbstractFileAction
{
    return app(UploadDraftAbstractFileAction::class);
}

/** @return array<int, SubmissionFile> */
function draftAbstractFileVersions(Submission $submission): array
{
    return SubmissionFile::query()
        ->where('submission_id', $submission->id)
        ->where('file_role', 'ABSTRACT_FILE')
        ->orderBy('manuscript_version')
        ->get()
        ->all();
}

function draftAbstractFileActivityCount(): int
{
    return DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_abstract_file_uploaded')
        ->count();
}

/** @return array<string, mixed>|null */
function draftAbstractFileActivityProperties(): ?array
{
    $activity = DB::table('activity_log')
        ->where('log_name', 'submission')
        ->where('event', 'submission_abstract_file_uploaded')
        ->orderByDesc('id')
        ->first();

    return $activity === null ? null : json_decode((string) $activity->properties, true);
}

test('a valid PDF draft abstract file is stored as file version 1 on the private disk', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $content = draftAbstractFilePdf('First draft abstract body');
    $file = draftAbstractFileUpload($content, 'My Abstract.PDF', 'application/zip');

    $version = draftAbstractFileAction()->handle($user, $submission, $file);

    $storedFile = $version->storedFile;

    expect($version->file_role)->toBe('ABSTRACT_FILE')
        ->and($version->manuscript_version)->toBe(1)
        ->and($version->status)->toBe('ACTIVE')
        ->and($version->supersedes_submission_file_id)->toBeNull()
        ->and($version->uploaded_by_user_id)->toBe($user->id)
        ->and($version->submitted_at)->not->toBeNull()
        ->and($storedFile->disk)->toBe('private')
        ->and($storedFile->visibility_class)->toBe('PRIVATE')
        ->and($storedFile->mime_type)->toBe('application/pdf')
        ->and($storedFile->size_bytes)->toBe(strlen($content))
        ->and($storedFile->checksum_sha256)->toBe(hash('sha256', $content))
        ->and($storedFile->original_name)->toBe('My Abstract.PDF')
        ->and($storedFile->path)->toStartWith('submission-files/abstracts/')
        ->and($storedFile->path)->toEndWith('.pdf')
        ->and(basename($storedFile->path))->not->toContain('Abstract')
        ->and(Str::isUuid(Str::beforeLast(basename($storedFile->path), '.'), 7))->toBeTrue();

    Storage::disk('private')->assertExists($storedFile->path);

    expect(Storage::disk('private')->get($storedFile->path))->toBe($content)
        ->and(draftAbstractFileActivityCount())->toBe(1);
});

test('a valid DOCX draft abstract file is stored as file version 1 on the private disk', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $content = draftAbstractFileDocx();
    $file = draftAbstractFileUpload($content, 'abstract.docx');

    $version = draftAbstractFileAction()->handle($user, $submission, $file);

    $storedFile = $version->storedFile;

    expect($version->file_role)->toBe('ABSTRACT_FILE')
        ->and($version->manuscript_version)->toBe(1)
        ->and($version->status)->toBe('ACTIVE')
        ->and($storedFile->disk)->toBe('private')
        ->and($storedFile->visibility_class)->toBe('PRIVATE')
        ->and($storedFile->mime_type)->toBe('application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        ->and($storedFile->size_bytes)->toBe(strlen($content))
        ->and($storedFile->checksum_sha256)->toBe(hash('sha256', $content))
        ->and($storedFile->path)->toEndWith('.docx')
        ->and(basename($storedFile->path))->not->toContain('abstract');

    Storage::disk('private')->assertExists($storedFile->path);

    expect(Storage::disk('private')->get($storedFile->path))->toBe($content);
});

test('a genuine DOCX package survives content detection that reports a generic ZIP mime', function () {
    $action = draftAbstractFileAction();
    $reflection = new ReflectionMethod(UploadDraftAbstractFileAction::class, 'assertDetectedMimeAcceptable');

    expect(fn () => $reflection->invoke($action, 'docx', 'application/zip'))
        ->not->toThrow(DomainException::class);

    expect(fn () => $reflection->invoke($action, 'docx', 'application/octet-stream'))
        ->not->toThrow(DomainException::class);

    expect(fn () => $reflection->invoke($action, 'docx', 'application/pdf'))
        ->toThrow(DomainException::class);

    expect(fn () => $reflection->invoke($action, 'pdf', 'application/zip'))
        ->toThrow(DomainException::class);

    expect(fn () => $reflection->invoke($action, 'pdf', 'text/plain'))
        ->toThrow(DomainException::class);

    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $file = draftAbstractFileUpload(draftAbstractFileDocx(), 'abstract.docx');
    $detected = strtolower((string) mime_content_type((string) $file->getRealPath()));

    $version = $action->handle($user, $submission, $file);

    expect(in_array($detected, [
        'application/zip',
        'application/x-zip',
        'application/x-zip-compressed',
        'application/octet-stream',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ], true))->toBeTrue()
        ->and($version->manuscript_version)->toBe(1)
        ->and($version->storedFile->mime_type)
        ->toBe('application/vnd.openxmlformats-officedocument.wordprocessingml.document');
});

test('DOCX content delivered under a .pdf extension is rejected without any write', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFileDocx(), 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(SubmissionFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([])
        ->and(draftAbstractFileActivityCount())->toBe(0);
});

test('PDF content delivered under a .docx extension is rejected without any write', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'abstract.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([])
        ->and(draftAbstractFileActivityCount())->toBe(0);
});

test('a truncated ZIP archive posing as DOCX is rejected without any write', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $truncated = substr(draftAbstractFileDocx(), 0, 40);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($truncated, 'abstract.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a ZIP archive without the Word OOXML package structure is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $plainZip = draftAbstractFileDocx([
        'omit' => ['[Content_Types].xml', '_rels/.rels', 'word/document.xml'],
        'entries' => ['readme.txt' => 'just a plain archive', 'data.csv' => 'a,b,c'],
    ]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($plainZip, 'abstract.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a forged DOCX without the Word document part is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $forged = draftAbstractFileDocx(['omit' => ['word/document.xml']]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($forged, 'abstract.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a forged DOCX carrying a non Word main content type is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $forged = draftAbstractFileDocx([
        'entries' => [
            '[Content_Types].xml' => str_replace(
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml',
                draftAbstractFileContentTypes(),
            ),
        ],
    ]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($forged, 'abstract.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a DOCX with a malformed content types part is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $malformed = draftAbstractFileDocx([
        'entries' => [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>',
        ],
    ]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($malformed, 'abstract.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a macro enabled document renamed to DOCX is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $macro = draftAbstractFileDocx([
        'entries' => [
            '[Content_Types].xml' => str_replace(
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml',
                'application/vnd.ms-word.document.macroEnabled.main+xml',
                draftAbstractFileContentTypes(),
            ),
            'word/vbaProject.bin' => str_repeat("\x00", 64),
        ],
    ]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($macro, 'abstract.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('an archive carrying a path traversal entry is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $traversal = draftAbstractFileDocx([
        'entries' => ['../evil.xml' => '<evil/>'],
    ]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($traversal, 'abstract.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a plain text file named as PDF is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload("This is not a PDF document at all.\n", 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a fabricated PDF that only carries the expected tokens is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $fabricated = "%PDF-1.4\nstartxref\n%%EOF\n";

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($fabricated, 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a truncated PDF without an end of file marker is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $truncated = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n";

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($truncated, 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('an unsupported file extension is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'abstract.doc'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a zero byte draft abstract file is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload('', 'empty.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a draft abstract file larger than 10 MiB is rejected', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        UploadedFile::fake()->create('oversize.pdf', 11000),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(SubmissionFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a failed PHP upload is rejected before any validation or write', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $file = draftAbstractFileUpload(
        draftAbstractFilePdf(),
        'abstract.pdf',
        null,
        UPLOAD_ERR_PARTIAL,
    );

    expect(fn () => draftAbstractFileAction()->handle($user, $submission, $file))
        ->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('the first draft abstract upload creates version 1 without touching official submission state', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $before = Submission::query()->whereKey($submission->id)->firstOrFail()->getAttributes();

    draftAbstractFileAction()->handle($user, $submission, draftAbstractFileUpload(draftAbstractFilePdf(), 'one.pdf'));

    $after = Submission::query()->whereKey($submission->id)->firstOrFail()->getAttributes();

    expect($after)->toBe($before)
        ->and($submission->fresh()->current_abstract_version)->toBe(0)
        ->and($submission->fresh()->academic_status)->toBe('DRAFT')
        ->and($submission->fresh()->submitted_at)->toBeNull()
        ->and(SubmissionSnapshot::query()->count())->toBe(0)
        ->and(draftAbstractFileVersions($submission))->toHaveCount(1)
        ->and(draftAbstractFileVersions($submission)[0]->manuscript_version)->toBe(1);
});

test('repeated uploads allocate sequential versions with a stable supersession chain', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $action = draftAbstractFileAction();

    $first = $action->handle($user, $submission, draftAbstractFileUpload(draftAbstractFilePdf('Body one'), 'one.pdf'));
    $second = $action->handle($user, $submission, draftAbstractFileUpload(draftAbstractFilePdf('Body two'), 'two.pdf'));
    $third = $action->handle($user, $submission, draftAbstractFileUpload(draftAbstractFilePdf('Body three'), 'three.pdf'));

    $versions = draftAbstractFileVersions($submission);

    expect(array_map(fn (SubmissionFile $file): int => $file->manuscript_version, $versions))->toBe([1, 2, 3])
        ->and(array_map(fn (SubmissionFile $file): string => $file->status, $versions))->toBe(['SUPERSEDED', 'SUPERSEDED', 'ACTIVE'])
        ->and($first->fresh()->status)->toBe('SUPERSEDED')
        ->and($second->fresh()->supersedes_submission_file_id)->toBe($first->id)
        ->and($third->fresh()->supersedes_submission_file_id)->toBe($second->id)
        ->and($first->fresh()->supersedes_submission_file_id)->toBeNull()
        ->and(StoredFile::query()->count())->toBe(3)
        ->and(SubmissionFile::query()->where('status', 'ACTIVE')->where('file_role', 'ABSTRACT_FILE')->count())->toBe(1)
        ->and($submission->fresh()->current_abstract_version)->toBe(0)
        ->and(draftAbstractFileActivityCount())->toBe(3);
});

test('previous file versions keep their private bytes and are never silently overwritten', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $action = draftAbstractFileAction();

    $firstContent = draftAbstractFilePdf('First retained body');
    $secondContent = draftAbstractFilePdf('Second retained body');

    $first = $action->handle($user, $submission, draftAbstractFileUpload($firstContent, 'one.pdf'));
    $firstPath = $first->storedFile->path;

    $second = $action->handle($user, $submission, draftAbstractFileUpload($secondContent, 'two.pdf'));
    $secondPath = $second->storedFile->path;

    expect($firstPath)->not->toBe($secondPath)
        ->and(Storage::disk('private')->get($firstPath))->toBe($firstContent)
        ->and(Storage::disk('private')->get($secondPath))->toBe($secondContent)
        ->and(Storage::disk('private')->allFiles())->toHaveCount(2)
        ->and($first->fresh()->stored_file_id)->not->toBe($second->stored_file_id)
        ->and($first->storedFile->checksum_sha256)->toBe(hash('sha256', $firstContent))
        ->and($second->storedFile->checksum_sha256)->toBe(hash('sha256', $secondContent))
        ->and($first->storedFile->path)->not->toBe($second->storedFile->path);
});

test('a byte identical reupload still creates a new numbered file version', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $action = draftAbstractFileAction();
    $content = draftAbstractFilePdf('Identical body');

    $first = $action->handle($user, $submission, draftAbstractFileUpload($content, 'same.pdf'));
    $second = $action->handle($user, $submission, draftAbstractFileUpload($content, 'same.pdf'));

    expect($second->manuscript_version)->toBe(2)
        ->and($second->id)->not->toBe($first->id)
        ->and($second->stored_file_id)->not->toBe($first->stored_file_id)
        ->and($second->storedFile->checksum_sha256)->toBe($first->storedFile->checksum_sha256)
        ->and($second->storedFile->path)->not->toBe($first->storedFile->path)
        ->and(Storage::disk('private')->allFiles())->toHaveCount(2)
        ->and($first->fresh()->status)->toBe('SUPERSEDED');
});

test('the original filename is sanitized to bounded metadata and never used as a path', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $content = draftAbstractFilePdf('Sanitized name body');
    $clientName = str_repeat('a', 300)." <draft>:final?|\x01\x7F.pdf";

    $version = draftAbstractFileAction()->handle($user, $submission, draftAbstractFileUpload($content, $clientName));

    $originalName = $version->storedFile->original_name;

    expect(strlen($originalName))->toBeLessThanOrEqual(255)
        ->and($originalName)->toEndWith('.pdf')
        ->and($originalName)->not->toContain('<')
        ->and($originalName)->not->toContain('>')
        ->and($originalName)->not->toContain(':')
        ->and($originalName)->not->toContain('?')
        ->and($originalName)->not->toContain('|')
        ->and($originalName)->not->toContain('..')
        ->and($originalName)->not->toContain("\x01")
        ->and($originalName)->not->toContain('/')
        ->and($version->storedFile->path)->not->toContain($originalName)
        ->and(Storage::disk('private')->allFiles())->toHaveCount(1);
});

test('the stored checksum is the server side SHA-256 of the stored private bytes', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $content = draftAbstractFileDocx();
    $version = draftAbstractFileAction()->handle($user, $submission, draftAbstractFileUpload($content, 'abstract.docx'));

    $storedBytes = Storage::disk('private')->get($version->storedFile->path);

    expect($version->storedFile->checksum_sha256)->toHaveLength(64)
        ->and($version->storedFile->checksum_sha256)->toBe(hash('sha256', $content))
        ->and($version->storedFile->checksum_sha256)->toBe(hash('sha256', (string) $storedBytes))
        ->and($version->storedFile->size_bytes)->toBe(strlen((string) $storedBytes));
});

test('abstract files are isolated on the private disk with private visibility only', function () {
    Storage::fake('private');
    Storage::fake('public');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $version = draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf('Private body'), 'private-abstract.pdf'),
    );

    expect($version->storedFile->disk)->toBe('private')
        ->and($version->storedFile->visibility_class)->toBe('PRIVATE')
        ->and($version->storedFile->path)->toStartWith('submission-files/abstracts/')
        ->and(StoredFile::query()->where('disk', '!=', 'private')->count())->toBe(0)
        ->and(StoredFile::query()->where('visibility_class', '!=', 'PRIVATE')->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([])
        ->and(Storage::disk('private')->allFiles())->toHaveCount(1);
});

test('a non owner actor cannot ingest a draft abstract file', function () {
    Storage::fake('private');

    ['submission' => $submission] = draftAbstractFileContext();

    $stranger = User::factory()->create();

    expect(fn () => draftAbstractFileAction()->handle(
        $stranger,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'stranger.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(SubmissionFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([])
        ->and(draftAbstractFileActivityCount())->toBe(0);
});

test('a membership from another conference edition is rejected without any write', function () {
    Storage::fake('private');

    $edition = draftAbstractFileEdition('ICHES27');
    $otherEdition = draftAbstractFileEdition('OTHER27');
    $package = draftAbstractFilePackage($edition);

    ['user' => $user, 'registration' => $registration] = draftAbstractFileRegistration($otherEdition, $package);

    $submission = draftAbstractFileSubmission($edition, $registration);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a registration package from another conference edition is rejected without any write', function () {
    Storage::fake('private');

    $edition = draftAbstractFileEdition('ICHES27');
    $otherEdition = draftAbstractFileEdition('OTHER27');
    $foreignPackage = draftAbstractFilePackage($otherEdition);

    ['user' => $user, 'registration' => $registration] = draftAbstractFileRegistration($edition, $foreignPackage);

    $submission = draftAbstractFileSubmission($edition, $registration);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a cancelled registration is rejected without any write', function () {
    Storage::fake('private');

    $edition = draftAbstractFileEdition();
    $package = draftAbstractFilePackage($edition);

    ['user' => $user, 'registration' => $registration] = draftAbstractFileRegistration($edition, $package, 'CANCELLED');

    $submission = draftAbstractFileSubmission($edition, $registration);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a historically inactive participation package does not block a draft abstract upload', function () {
    Storage::fake('private');

    ['package' => $package, 'user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    ParticipationPackage::query()
        ->whereKey($package->id)
        ->update(['active' => false]);

    $version = draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf('Inactive package body'), 'abstract.pdf'),
    );

    expect($version->manuscript_version)->toBe(1)
        ->and($package->fresh()->active)->toBeFalse()
        ->and($version->submission->registration->package->edition_id)->toBe($version->submission->edition_id);
});

test('registration payment states do not gate draft abstract uploads', function () {
    Storage::fake('private');

    ['registration' => $registration, 'user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $action = draftAbstractFileAction();
    $sequence = 0;

    foreach (['PENDING', 'PAYMENT_PENDING', 'CONFIRMED'] as $status) {
        Registration::query()
            ->whereKey($registration->id)
            ->update(['status' => $status]);

        $version = $action->handle(
            $user,
            $submission,
            draftAbstractFileUpload(draftAbstractFilePdf("Body for {$status}"), 'abstract.pdf'),
        );

        $sequence++;

        expect($version->manuscript_version)->toBe($sequence)
            ->and($registration->fresh()->status->value)->toBe($status)
            ->and($submission->fresh()->academic_status)->toBe('DRAFT')
            ->and($submission->fresh()->current_abstract_version)->toBe(0);
    }

    expect(Payment::query()->count())->toBe(0)
        ->and(draftAbstractFileVersions($submission))->toHaveCount(3);
});

test('a non draft submission cannot receive a draft abstract file', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext([
        'academic_status' => 'SUBMITTED',
        'submitted_at' => now(),
    ]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([])
        ->and(draftAbstractFileActivityCount())->toBe(0);
});

test('a stale caller draft model cannot bypass the authoritative non draft guard', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $staleSubmission = Submission::query()->whereKey($submission->id)->firstOrFail();

    expect($staleSubmission->academic_status)->toBe('DRAFT');

    Submission::query()
        ->whereKey($submission->id)
        ->update(['academic_status' => 'SUBMITTED']);

    expect($staleSubmission->academic_status)->toBe('DRAFT');

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $staleSubmission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'abstract.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([])
        ->and($submission->fresh()->academic_status)->toBe('SUBMITTED');
});

test('a private storage write failure fails the upload without leaving records', function () {
    $failingDisk = Mockery::mock(FilesystemAdapter::class);

    $failingDisk->shouldReceive('put')
        ->once()
        ->withArgs(fn (mixed $path, mixed $stream): bool => is_string($path)
            && str_starts_with($path, 'submission-files/abstracts/')
            && is_resource($stream))
        ->andReturn(false);

    $failingDisk->shouldReceive('delete')->once()->andReturn(true);
    $failingDisk->shouldReceive('exists')->once()->andReturn(false);

    Storage::set('private', $failingDisk);

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'abstract.pdf'),
    ))->toThrow(RuntimeException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(SubmissionFile::query()->count())->toBe(0)
        ->and(draftAbstractFileActivityCount())->toBe(0);

    Mockery::close();
});

test('a failure during transactional activity insertion rolls back records and removes only the new bytes', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $action = draftAbstractFileAction();

    $firstContent = draftAbstractFilePdf('Retained first body');
    $first = $action->handle($user, $submission, draftAbstractFileUpload($firstContent, 'one.pdf'));
    $firstPath = $first->storedFile->path;

    // Deliberately fail after the INSERT query, while the transaction is
    // still active. Never DROP tables: MySQL DDL implicitly commits.
    DB::listen(function (QueryExecuted $event): void {
        $sql = strtolower(ltrim($event->sql));

        if (str_starts_with($sql, 'insert') && str_contains($sql, 'activity_log')) {
            throw new RuntimeException('Simulated database transaction failure after activity insertion.');
        }
    });

    $secondContent = draftAbstractFilePdf('Failed second body');

    expect(fn () => $action->handle($user, $submission, draftAbstractFileUpload($secondContent, 'two.pdf')))
        ->toThrow(RuntimeException::class);

    expect(StoredFile::query()->count())->toBe(1)
        ->and(SubmissionFile::query()->count())->toBe(1)
        ->and($first->fresh()->status)->toBe('ACTIVE')
        ->and($first->fresh()->manuscript_version)->toBe(1)
        ->and(SubmissionFile::query()->where('manuscript_version', 2)->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([$firstPath])
        ->and(Storage::disk('private')->get($firstPath))->toBe($firstContent)
        ->and(draftAbstractFileActivityCount())->toBe(1);
});

test('the audit evidence contains safe identifiers and version information only', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $originalName = 'ConfidentialManuscript-XYZ.pdf';
    $content = draftAbstractFilePdf('Confidential manuscript body marker');

    $version = draftAbstractFileAction()->handle($user, $submission, draftAbstractFileUpload($content, $originalName));

    $properties = draftAbstractFileActivityProperties();

    expect($properties)->not->toBeNull();

    $serialized = (string) json_encode($properties);

    foreach ([
        $originalName,
        'ConfidentialManuscript',
        'Confidential manuscript body',
        $version->storedFile->checksum_sha256,
        $version->storedFile->path,
        'submission-files/abstracts',
        'application/pdf',
        $user->email,
    ] as $secret) {
        expect($serialized)->not->toContain($secret);
    }

    $actualKeys = array_keys($properties);
    $expectedKeys = [
        'conference_edition_id',
        'registration_id',
        'paper_code',
        'file_role',
        'submission_file_id',
        'stored_file_id',
        'manuscript_version',
        'supersedes_submission_file_id',
        'is_replacement',
    ];

    sort($actualKeys);
    sort($expectedKeys);

    expect($actualKeys)->toBe($expectedKeys)
        ->and($properties['conference_edition_id'])->toBe($submission->edition_id)
        ->and($properties['registration_id'])->toBe($submission->registration_id)
        ->and($properties['paper_code'])->toBe($submission->paper_code)
        ->and($properties['file_role'])->toBe('ABSTRACT_FILE')
        ->and($properties['submission_file_id'])->toBe($version->id)
        ->and($properties['stored_file_id'])->toBe($version->stored_file_id)
        ->and($properties['manuscript_version'])->toBe(1)
        ->and($properties['supersedes_submission_file_id'])->toBeNull()
        ->and($properties['is_replacement'])->toBeFalse()
        ->and((string) DB::table('activity_log')->orderByDesc('id')->first()->description)
        ->not->toContain($originalName);
});

test('a replacement records privacy safe supersession evidence without private file details', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $action = draftAbstractFileAction();

    $first = $action->handle($user, $submission, draftAbstractFileUpload(draftAbstractFilePdf('First body'), 'first.pdf'));
    $second = $action->handle($user, $submission, draftAbstractFileUpload(draftAbstractFilePdf('Second body'), 'second.pdf'));

    $properties = draftAbstractFileActivityProperties();

    expect($properties['manuscript_version'])->toBe(2)
        ->and($properties['supersedes_submission_file_id'])->toBe($first->id)
        ->and($properties['is_replacement'])->toBeTrue()
        ->and($properties['submission_file_id'])->toBe($second->id)
        ->and(json_encode($properties))->not->toContain($first->storedFile->path)
        ->and(json_encode($properties))->not->toContain($second->storedFile->path);
});

test('draft abstract uploads never create snapshots or mutate official submission facts', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext([
        'academic_status' => 'SUBMITTED',
        'submitted_at' => now(),
    ]);

    Submission::query()
        ->whereKey($submission->id)
        ->update(['academic_status' => 'DRAFT', 'submitted_at' => null]);

    $before = Submission::query()->whereKey($submission->id)->firstOrFail()->getAttributes();

    $action = draftAbstractFileAction();
    $action->handle($user, $submission, draftAbstractFileUpload(draftAbstractFilePdf('Snapshot body one'), 'one.pdf'));
    $action->handle($user, $submission, draftAbstractFileUpload(draftAbstractFilePdf('Snapshot body two'), 'two.pdf'));

    $after = Submission::query()->whereKey($submission->id)->firstOrFail()->getAttributes();

    expect($after)->toBe($before)
        ->and(SubmissionSnapshot::query()->count())->toBe(0)
        ->and($submission->fresh()->current_abstract_version)->toBe(0)
        ->and($submission->fresh()->submitted_at)->toBeNull()
        ->and($submission->fresh()->academic_status)->toBe('DRAFT')
        ->and(draftAbstractFileVersions($submission))->toHaveCount(2);
});

test('legacy ABSTRACT file rows are untouched by ABSTRACT_FILE version allocation', function () {
    Storage::fake('private');

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $legacyStoredFile = StoredFile::query()->create([
        'disk' => 'private',
        'path' => 'submissions/legacy-abstract.pdf',
        'original_name' => 'legacy.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 100,
        'checksum_sha256' => str_repeat('a', 64),
        'visibility_class' => 'PRIVATE',
        'uploaded_by_user_id' => $user->id,
    ]);

    $legacyFile = SubmissionFile::query()->create([
        'submission_id' => $submission->id,
        'stored_file_id' => $legacyStoredFile->id,
        'file_role' => 'ABSTRACT',
        'manuscript_version' => 1,
        'uploaded_by_user_id' => $user->id,
        'submitted_at' => now(),
        'status' => 'ACTIVE',
    ]);

    $version = draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf('Legacy sibling body'), 'abstract.pdf'),
    );

    $legacyAfter = SubmissionFile::query()->findOrFail($legacyFile->id);

    expect($version->manuscript_version)->toBe(1)
        ->and($version->file_role)->toBe('ABSTRACT_FILE')
        ->and($version->supersedes_submission_file_id)->toBeNull()
        ->and($legacyAfter->status)->toBe('ACTIVE')
        ->and($legacyAfter->manuscript_version)->toBe(1)
        ->and($legacyAfter->file_role)->toBe('ABSTRACT')
        ->and($legacyAfter->stored_file_id)->toBe($legacyStoredFile->id)
        ->and($legacyStoredFile->fresh()->path)->toBe('submissions/legacy-abstract.pdf')
        ->and(Storage::disk('private')->allFiles())->toHaveCount(1)
        ->and(Storage::disk('private')->allFiles())->not->toContain('submissions/legacy-abstract.pdf');
});

test('a forged PDF with superficial PDF tokens and no cross reference table is rejected', function () {
    Storage::fake('private');
    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $forgery = "%PDF-1.4\nTHIS IS NOT A VALID PDF STRUCTURE\n1 0 obj\n/Root\nendobj\nstartxref\n0\n%%EOF\n";

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($forgery, 'forged.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a PDF with a cross reference pointer to the wrong byte offset is rejected', function () {
    Storage::fake('private');
    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $valid = draftAbstractFilePdf();
    $invalid = preg_replace('/startxref\s+\d+\s+%%EOF/', "startxref\n1\n%%EOF", $valid);

    expect($invalid)->not->toBeNull();
    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload((string) $invalid, 'wrong-offset.pdf'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a DOCX with superficially plausible but malformed content types XML is rejected', function () {
    Storage::fake('private');
    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $invalid = draftAbstractFileDocx(['entries' => [
        '[Content_Types].xml' => '<?xml version="1.0"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml">'
            .'</Types>',
    ]]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($invalid, 'malformed.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('a DOCX with textual Word namespace but no XML document root is rejected', function () {
    Storage::fake('private');
    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $invalid = draftAbstractFileDocx(['entries' => [
        'word/document.xml' => 'not XML: schemas.openxmlformats.org/wordprocessingml/2006/main',
    ]]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($invalid, 'forged-docx.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('DOCX XML with external entity declarations is rejected', function () {
    Storage::fake('private');
    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $invalid = draftAbstractFileDocx(['entries' => [
        'word/document.xml' => '<?xml version="1.0"?>'
            .'<!DOCTYPE w:document [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            .'<w:body>&xxe;</w:body></w:document>',
    ]]);

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($invalid, 'external-entity.docx'),
    ))->toThrow(DomainException::class);

    expect(StoredFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
});

test('DOCX media entries larger than one MiB remain accepted within the 10 MiB file policy', function () {
    Storage::fake('private');
    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $content = draftAbstractFileDocx(['entries' => [
        'word/media/image1.png' => random_bytes(1300000),
    ]]);

    expect(strlen($content))->toBeGreaterThan(1048576)
        ->toBeLessThanOrEqual(10485760);

    $version = draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload($content, 'abstract-with-image.docx'),
    );

    expect($version->manuscript_version)->toBe(1)
        ->and($version->storedFile->size_bytes)->toBe(strlen($content))
        ->and(Storage::disk('private')->get($version->storedFile->path))->toBe($content);
});

test('PDF 1.5 with a genuine cross reference stream remains uploadable', function () {
    Storage::fake('private');
    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $original = draftAbstractFilePdf();
    $start = strpos($original, "xref\n");
    expect($start)->not->toBeFalse();

    $prefix = substr($original, 0, (int) $start);
    $offsets = [];
    for ($i = 1; $i <= 5; $i++) {
        $offset = strpos($prefix, $i." 0 obj\n");
        expect($offset)->not->toBeFalse();
        $offsets[$i] = (int) $offset;
    }

    $xrefOffset = strlen($prefix);
    $xrefBytes = pack('CNn', 0, 0, 65535);
    foreach ($offsets as $offset) {
        $xrefBytes .= pack('CNn', 1, $offset, 0);
    }
    $xrefBytes .= pack('CNn', 1, $xrefOffset, 0);

    $content = str_replace('%PDF-1.4', '%PDF-1.5', $prefix)
        ."6 0 obj\n<< /Type /XRef /Root 1 0 R /Size 7 /W [1 4 2] /Length ".strlen($xrefBytes)." >>\n"
        ."stream\n".$xrefBytes."\nendstream\nendobj\n"
        ."startxref\n".$xrefOffset."\n%%EOF\n";

    $file = draftAbstractFileUpload($content, 'xref-stream.pdf');
    $stored = draftAbstractFileAction()->handle($user, $submission, $file);

    expect($stored->manuscript_version)->toBe(1)
        ->and($stored->storedFile->mime_type)->toBe('application/pdf')
        ->and(Storage::disk('private')->get($stored->storedFile->path))->toBe($content);
});

test('a failed private cleanup delete surfaces orphan risk and never reports success', function () {
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('put')->once()->andReturn(false);
    $disk->shouldReceive('delete')->once()->andReturn(false);
    $disk->shouldReceive('exists')->once()->andReturn(true);
    Storage::set('private', $disk);

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'cleanup-failed.pdf'),
    ))->toThrow(RuntimeException::class, 'could not be removed');

    expect(StoredFile::query()->count())->toBe(0)
        ->and(SubmissionFile::query()->count())->toBe(0)
        ->and(draftAbstractFileActivityCount())->toBe(0);
});

test('a throwing private cleanup check surfaces the original upload failure', function () {
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('put')->once()->andReturn(false);
    $disk->shouldReceive('delete')->once()->andReturn(true);
    $disk->shouldReceive('exists')->once()->andThrow(new RuntimeException('Test storage unavailable'));
    Storage::set('private', $disk);

    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    try {
        draftAbstractFileAction()->handle(
            $user,
            $submission,
            draftAbstractFileUpload(draftAbstractFilePdf(), 'cleanup-exception.pdf'),
        );
        Assert::fail('Cleanup failure must propagate.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toContain('could not be removed')
            ->and($exception->getPrevious()?->getMessage())->toContain('Unable to store');
    }

    expect(StoredFile::query()->count())->toBe(0)
        ->and(SubmissionFile::query()->count())->toBe(0);
});

test('MySQL locks ownership and package rows in the authoritative upload transaction', function () {
    Storage::fake('private');
    ['user' => $user, 'submission' => $submission] = draftAbstractFileContext();

    $queries = [];
    DB::listen(function (QueryExecuted $event) use (&$queries): void {
        if (str_contains(strtolower($event->sql), 'for update')) {
            $queries[] = strtolower($event->sql);
        }
    });

    $version = draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'locked.pdf'),
    );

    $tables = ['submissions', 'registrations', 'edition_memberships', 'participation_packages', 'submission_files'];
    $positions = [];

    foreach ($tables as $table) {
        $found = array_search(true, array_map(
            fn (string $sql): bool => str_contains($sql, $table),
            $queries,
        ), true);
        expect($found)->not->toBeFalse();
        $positions[] = $found;
    }

    expect($positions)->toBe([0, 1, 2, 3, 4])
        ->and($version->manuscript_version)->toBe(1);
})->skip(fn (): bool => DB::connection()->getDriverName() !== 'mysql', 'Requires MySQL row locks.');

test('MySQL aborts and cleans up if membership edition changes after upload precheck', function () {
    Storage::fake('private');
    ['user' => $user, 'membership' => $membership, 'submission' => $submission] = draftAbstractFileContext();
    $otherEdition = draftAbstractFileEdition('ICHES28');

    $changed = false;
    DB::listen(function (QueryExecuted $event) use (&$changed, $membership, $otherEdition): void {
        if ($changed || ! str_contains(strtolower($event->sql), 'for update')
            || ! str_contains(strtolower($event->sql), 'registrations')) {
            return;
        }

        $changed = true;
        EditionMembership::query()->whereKey($membership->id)->update(['edition_id' => $otherEdition->id]);
    });

    expect(fn () => draftAbstractFileAction()->handle(
        $user,
        $submission,
        draftAbstractFileUpload(draftAbstractFilePdf(), 'stale-edition.pdf'),
    ))->toThrow(DomainException::class);

    expect($changed)->toBeTrue()
        ->and($membership->fresh()->edition_id)->toBe($submission->edition_id)
        ->and(StoredFile::query()->count())->toBe(0)
        ->and(SubmissionFile::query()->count())->toBe(0)
        ->and(Storage::disk('private')->allFiles())->toBe([]);
})->skip(fn (): bool => DB::connection()->getDriverName() !== 'mysql', 'Requires MySQL transactional revalidation.');
