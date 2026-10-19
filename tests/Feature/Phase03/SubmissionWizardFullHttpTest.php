<?php

use App\Actions\Submission\CreateDraftSubmissionAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\EditionMembership;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->withoutVite();

    $series = ConferenceSeries::query()->create(['code' => 'ICHES-E2E', 'name' => 'ICHES', 'status' => 'ACTIVE']);
    $this->edition = ConferenceEdition::query()->create([
        'series_id' => $series->id,
        'edition_code' => 'ICHES-2027-E2E',
        'edition_number' => 6, 'year' => 2027, 'host_name' => 'UNISYA',
        'mode' => 'HYBRID', 'timezone' => 'Asia/Jakarta',
        'starts_at' => now()->addMonth(),
        'ends_at' => now()->addMonth()->addDays(2),
        'lifecycle_status' => 'DRAFT',
        'settings_json' => [
            'unrelated' => ['preserve' => true],
            'abstract_submission' => [
                'min_keywords' => 3, 'max_keywords' => 5,
                'track_required' => false, 'abstract_file_required' => false,
            ],
        ],
    ]);
    $package = ParticipationPackage::query()->create([
        'edition_id' => $this->edition->id, 'code' => 'PRESENTER',
        'name_i18n' => ['id' => 'Pemakalah', 'en' => 'Presenter', 'ar' => 'مقدم'],
        'billing_mode' => 'PAID', 'price' => '750000.00', 'currency_code' => 'IDR',
        'active' => true, 'display_order' => 1,
    ]);
    $this->actor = User::factory()->create();
    $membership = EditionMembership::query()->create([
        'edition_id' => $this->edition->id, 'user_id' => $this->actor->id,
        'membership_status' => 'ACTIVE', 'joined_at' => now(),
    ]);
    $this->registration = Registration::query()->create([
        'membership_id' => $membership->id,
        'registration_code' => 'E2E-'.Str::random(8),
        'package_id' => $package->id,
        'package_name_snapshot' => 'Presenter',
        'billing_mode' => 'PAID', 'fee_amount' => '750000.00',
        'currency_code' => 'IDR', 'status' => 'PAYMENT_PENDING',
    ]);
    $this->draft = app(CreateDraftSubmissionAction::class)
        ->handle($this->actor, $this->edition, $this->registration, 'id');
});

test('readiness blocks a blank draft and official submission cannot invent records', function () {
    $this->actingAs($this->actor)->get(route('submissions.details', $this->draft))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('submissions/Details')
            ->where('readiness.status', 'BLOCKED'));
    $this->actingAs($this->actor)->post(route('submissions.submit', $this->draft))
        ->assertSessionHasErrors('submit');
    $this->assertDatabaseCount('submission_snapshots', 0);
    expect($this->draft->fresh()->academic_status)->toBe('DRAFT');
});

test('corresponding author can save metadata and submit a PAID-registration abstract without paying first', function () {
    $this->actingAs($this->actor)->put(route('submissions.details.update', $this->draft), [
        'track_id' => null, 'title' => 'The Impact of Community Assets',
        'subtitle' => null, 'abstract' => 'Evidence from an Islamic higher education case study.',
        'keywords_text' => "community\neducation\nhumanity",
    ])->assertSessionHasNoErrors();

    $this->actingAs($this->actor)->put(route('submissions.contributors.update', $this->draft), [
        'contributors' => [[
            'id' => null, 'display_name' => 'Test Scholar',
            'given_name' => 'Test', 'family_name' => 'Scholar',
            'single_name' => false, 'email' => $this->actor->email,
            'country_code' => 'ID', 'orcid' => null,
            'is_corresponding' => true,
            'affiliations' => [[
                'institution_id' => null,
                'institution_name' => 'Universitas Islam Syarifuddin Lumajang',
                'subdivision' => null, 'city' => 'Lumajang', 'country_code' => 'ID',
            ]],
        ]],
    ])->assertSessionHasNoErrors();

    $this->actingAs($this->actor)->put(route('submissions.references.update', $this->draft), [
        'references' => [[
            'id' => null, 'raw_citation' => 'Test Scholar. "Community Assets." 2026.',
            'doi' => null, 'url' => null,
        ]],
    ])->assertSessionHasNoErrors();

    $this->edition->workflowWindows()->create([
        'window_code' => 'ABSTRACT_SUBMISSION',
        'opens_at' => now()->subDay(),
        'closes_at' => now()->addDay(),
        'active' => true,
    ]);

    $this->actingAs($this->actor)->get(route('submissions.details', $this->draft))
        ->assertOk()->assertInertia(fn ($page) => $page
        ->where('readiness.status', 'WARNING')
        ->has('contributors', 1)->has('references', 1));

    $this->actingAs($this->actor)->post(route('submissions.submit', $this->draft))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('submissions.details', $this->draft));

    $this->assertDatabaseHas('submissions', [
        'id' => $this->draft->id, 'academic_status' => 'SUBMITTED',
        'current_abstract_version' => 1,
    ]);
    $this->assertDatabaseHas('registrations', [
        'id' => $this->registration->id, 'status' => 'PAYMENT_PENDING',
    ]);
    $this->assertDatabaseCount('submission_snapshots', 1);
    expect(Activity::query()->where('event', 'submission_officially_submitted')->count())->toBe(1);

    $this->actingAs($this->actor)->put(route('submissions.references.update', $this->draft), [
        'references' => [],
    ])->assertStatus(409);
    $this->actingAs($this->actor)->post(route('submissions.submit', $this->draft))
        ->assertStatus(409);
    $this->assertDatabaseCount('submission_snapshots', 1);
});

test('foreign user cannot mutate contributors, references, file upload, or submit owned drafts', function () {
    $stranger = User::factory()->create();
    $this->actingAs($stranger)->put(route('submissions.contributors.update', $this->draft), [
        'contributors' => [],
    ])->assertNotFound();
    $this->actingAs($stranger)->put(route('submissions.references.update', $this->draft), [
        'references' => [],
    ])->assertNotFound();
    $this->actingAs($stranger)->post(route('submissions.submit', $this->draft))
        ->assertNotFound();
    $this->assertDatabaseCount('submission_snapshots', 0);
});

test('superadmin can configure canonical abstract policy without removing other edition settings', function () {
    $admin = User::factory()->superAdmin()->create();
    $this->actingAs($admin)->post(route('admin.editions.abstract-policy.store', $this->edition), [
        'min_keywords' => 4, 'max_keywords' => 6,
        'track_required' => true, 'abstract_file_required' => true,
    ])->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.editions.academic', $this->edition));
    $settings = $this->edition->fresh()->settings_json;
    expect($settings['abstract_submission'])->toBe([
        'min_keywords' => 4, 'max_keywords' => 6,
        'track_required' => true, 'abstract_file_required' => true,
    ])->and($settings['unrelated']['preserve'])->toBeTrue();
    expect(Activity::query()->where('event', 'edition_abstract_policy_configured')->count())->toBe(1);
});

test('abstract policy mutations are protected and reject reversed keyword constraints', function () {
    $this->actingAs($this->actor)->post(route('admin.editions.abstract-policy.store', $this->edition), [
        'min_keywords' => 2, 'max_keywords' => 5,
        'track_required' => false, 'abstract_file_required' => false,
    ])->assertForbidden();

    $this->actingAs(User::factory()->superAdmin()->create())
        ->post(route('admin.editions.abstract-policy.store', $this->edition), [
            'min_keywords' => 7, 'max_keywords' => 4,
            'track_required' => false, 'abstract_file_required' => false,
        ])->assertSessionHasErrors('max_keywords');
    expect(Activity::query()->where('event', 'edition_abstract_policy_configured')->count())->toBe(0);
});

function p03iHttpPdf(string $body = 'Draft abstract body'): string
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

test('private abstract upload endpoint saves a real PDF version and rejects a spoofed file', function () {
    Storage::fake('private');
    $path = tempnam(sys_get_temp_dir(), 'iches-http-');
    file_put_contents($path, p03iHttpPdf());

    try {
        $file = new UploadedFile($path, 'abstract.pdf', 'application/pdf', null, true);
        $this->actingAs($this->actor)->post(route('submissions.abstract-file.store', $this->draft), [
            'file' => $file,
        ])->assertSessionHasNoErrors()->assertRedirect(route('submissions.details', $this->draft));
    } finally {
        @unlink($path);
    }

    $this->assertDatabaseHas('submission_files', [
        'submission_id' => $this->draft->id,
        'file_role' => 'ABSTRACT_FILE',
        'manuscript_version' => 1,
    ]);
    expect(Storage::disk('private')->allFiles())->toHaveCount(1);

    $fake = UploadedFile::fake()->createWithContent('spoof.pdf', 'not-a-pdf');
    $this->actingAs($this->actor)->post(route('submissions.abstract-file.store', $this->draft), [
        'file' => $fake,
    ])->assertSessionHasErrors('file');
    $this->assertDatabaseCount('submission_files', 1);
});
