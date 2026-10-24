<?php

use App\Http\Controllers\Academic\AcademicController;
use App\Http\Controllers\Academic\ReviewerTopicEligibilityController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ConferenceEditionController;
use App\Http\Controllers\Admin\ConferenceSeriesController;
use App\Http\Controllers\Admin\EditionAcademicSetupController;
use App\Http\Controllers\Admin\EditionConfigurationController;
use App\Http\Controllers\Localization\UpdateLocaleController;
use App\Http\Controllers\Payment\DownloadPaymentProofController;
use App\Http\Controllers\Payment\RequestPaymentCorrectionController;
use App\Http\Controllers\Payment\SubmitPaymentProofController;
use App\Http\Controllers\Payment\VerifyPaymentController;
use App\Http\Controllers\Registration\EventPassQrController;
use App\Http\Controllers\Registration\ParticipantDashboardController;
use App\Http\Controllers\Registration\ShowEventPassController;
use App\Http\Controllers\Registration\ShowParticipantRegistrationController;
use App\Http\Controllers\Registration\StoreParticipantRegistrationController;
use App\Http\Controllers\Submission\AuthorSubmissionController;
use App\Http\Controllers\Submission\AuthorSubmissionStagesController;
use App\Http\Controllers\Submission\InstitutionLookupController;
use App\Http\Controllers\Workspace\RoleOverviewController;
use App\Http\Controllers\Workspace\SwitchSuperAdminWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::post('locale', UpdateLocaleController::class)->name('locale.update');

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('academic/reviewer-topics', [ReviewerTopicEligibilityController::class, 'index'])->name('academic.reviewer-topics.index');
    Route::post('academic/reviewer-topics/edition', [ReviewerTopicEligibilityController::class, 'selectEdition'])->name('academic.reviewer-topics.edition');
    Route::post('academic/reviewer-topics', [ReviewerTopicEligibilityController::class, 'store'])->name('academic.reviewer-topics.store');
    Route::put('academic/reviewer-topics', [ReviewerTopicEligibilityController::class, 'sync'])->name('academic.reviewer-topics.sync');
    Route::delete('academic/reviewer-topics/{eligibility}', [ReviewerTopicEligibilityController::class, 'destroy'])->name('academic.reviewer-topics.destroy');
    Route::post('academic/submissions/{submission}/topic', [AcademicController::class, 'correctTopic'])->name('academic.topic.correct');
    Route::get('academic', [AcademicController::class, 'index'])->name('academic.index');
    Route::post('academic/edition', [AcademicController::class, 'selectEdition'])->name('academic.edition');
    Route::get('academic/submissions/{submission}', [AcademicController::class, 'show'])->name('academic.show');
    Route::get('academic/submissions/{submission}/file', [AcademicController::class, 'committeeFile'])->name('academic.file');
    Route::post('academic/submissions/{submission}/screen', [AcademicController::class, 'screen'])->name('academic.screen');
    Route::post('academic/submissions/{submission}/assign', [AcademicController::class, 'assign'])->name('academic.assign');
    Route::post('academic/submissions/{submission}/decide', [AcademicController::class, 'decide'])->name('academic.decide');
    Route::post('academic/assignments/{assignment}/cancel', [AcademicController::class, 'cancel'])->name('academic.cancel');
    Route::get('reviews', [AcademicController::class, 'reviews'])->name('academic.reviews');
    Route::get('reviews/{assignment}', [AcademicController::class, 'review'])->name('academic.review');
    Route::get('reviews/{assignment}/file', [AcademicController::class, 'reviewFile'])->name('academic.review.file');
    Route::post('reviews/{assignment}/respond', [AcademicController::class, 'respond'])->name('academic.respond');
    Route::put('reviews/{assignment}', [AcademicController::class, 'saveReview'])->name('academic.review.save');
    Route::get('dashboard/submissions/{submission}/academic', [AcademicController::class, 'author'])->name('academic.author');
    Route::get('dashboard/submissions/{submission}/academic/file', [AcademicController::class, 'authorFile'])->name('academic.author.file');
    Route::post('dashboard/submissions/{submission}/academic/revision', [AcademicController::class, 'revise'])->name('academic.revise');
    Route::get('documents/presentation-loa/{document}', [AcademicController::class, 'loa'])->name('academic.loa');
    Route::post('workspace-mode', SwitchSuperAdminWorkspaceController::class)
        ->middleware('throttle:12,1')->name('workspace.mode.switch');
    Route::get('workspace', RoleOverviewController::class)
        ->name('workspace.overview');
    Route::get('admin/dashboard', AdminDashboardController::class)
        ->name('admin.dashboard');

    Route::get('admin/editions', [ConferenceEditionController::class, 'index'])
        ->name('admin.editions.index');
    Route::get('admin/editions/create', [ConferenceEditionController::class, 'create'])
        ->name('admin.editions.create');
    Route::post('admin/editions', [ConferenceEditionController::class, 'store'])
        ->name('admin.editions.store');
    Route::delete('admin/editions/{edition}', [ConferenceEditionController::class, 'destroy'])
        ->name('admin.editions.destroy');

    Route::get('admin/editions/{edition}/configuration', [EditionConfigurationController::class, 'show'])
        ->name('admin.editions.configuration');
    Route::post('admin/editions/{edition}/payment-destinations', [EditionConfigurationController::class, 'storeDestination'])
        ->name('admin.editions.destinations.store');
    Route::post('admin/editions/{edition}/packages', [EditionConfigurationController::class, 'storePackage'])
        ->name('admin.editions.packages.store');
    Route::get('admin/editions/{edition}/academic', [EditionAcademicSetupController::class, 'show'])
        ->name('admin.editions.academic');
    Route::post('admin/editions/{edition}/tracks', [EditionAcademicSetupController::class, 'storeTrack'])
        ->name('admin.editions.tracks.store');
    Route::post('admin/editions/{edition}/windows', [EditionAcademicSetupController::class, 'storeWindow'])
        ->name('admin.editions.windows.store');
    Route::post('admin/editions/{edition}/abstract-policy', [EditionAcademicSetupController::class, 'storeAbstractPolicy'])
        ->name('admin.editions.abstract-policy.store');

    Route::get('admin/series', [ConferenceSeriesController::class, 'index'])
        ->name('admin.series.index');
    Route::get('admin/series/create', [ConferenceSeriesController::class, 'create'])
        ->name('admin.series.create');
    Route::post('admin/series', [ConferenceSeriesController::class, 'store'])
        ->name('admin.series.store');
    Route::delete('admin/series/{series}', [ConferenceSeriesController::class, 'destroy'])
        ->name('admin.series.destroy');

    Route::get(
        'dashboard',
        ParticipantDashboardController::class,
    )->name('dashboard');

    Route::get('dashboard/institutions/search', [InstitutionLookupController::class, 'search'])->middleware('throttle:30,1')->name('institutions.search');
    Route::post('dashboard/institutions/resolve', [InstitutionLookupController::class, 'resolve'])->middleware('throttle:12,1')->name('institutions.resolve');
    Route::get('dashboard/submissions', [AuthorSubmissionController::class, 'index'])->name('submissions.index');
    Route::post('dashboard/submissions', [AuthorSubmissionController::class, 'store'])->name('submissions.store');
    Route::delete('dashboard/submissions/{submission}', [AuthorSubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::get('dashboard/submissions/{submission}/details', [AuthorSubmissionController::class, 'details'])->name('submissions.details');
    Route::put('dashboard/submissions/{submission}/details', [AuthorSubmissionController::class, 'update'])->name('submissions.details.update');
    Route::put('dashboard/submissions/{submission}/contributors', [AuthorSubmissionStagesController::class, 'contributors'])->name('submissions.contributors.update');
    Route::put('dashboard/submissions/{submission}/references', [AuthorSubmissionStagesController::class, 'references'])->name('submissions.references.update');
    Route::post('dashboard/submissions/{submission}/abstract-file', [AuthorSubmissionStagesController::class, 'upload'])->name('submissions.abstract-file.store');
    Route::post('dashboard/submissions/{submission}/submit', [AuthorSubmissionStagesController::class, 'submit'])->middleware('throttle:6,1')->name('submissions.submit');

    Route::get(
        'editions/{edition}/registration',
        ShowParticipantRegistrationController::class,
    )->name('editions.registration.show');

    Route::post(
        'editions/{edition}/registration',
        StoreParticipantRegistrationController::class,
    )->name('editions.registration.store');

    Route::get(
        'documents/event-pass/{document}',
        ShowEventPassController::class,
    )->name('documents.event-pass.show');

    Route::get(
        'documents/event-pass/{document}/qr.svg',
        EventPassQrController::class,
    )->name('documents.event-pass.qr');

    Route::post(
        'payments/{payment}/proof',
        SubmitPaymentProofController::class,
    )->name('payments.proofs.store');

    Route::get(
        'payments/{payment}/proofs/{proof}/download',
        DownloadPaymentProofController::class,
    )->name('payments.proofs.download');

    Route::post(
        'payments/{payment}/correction',
        RequestPaymentCorrectionController::class,
    )->name('payments.correction.store');

    Route::post(
        'payments/{payment}/verify',
        VerifyPaymentController::class,
    )->name('payments.verify');
});

require __DIR__.'/settings.php';
