<?php

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
use Illuminate\Support\Facades\Route;

Route::post('locale', UpdateLocaleController::class)->name('locale.update');

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get(
        'dashboard',
        ParticipantDashboardController::class,
    )->name('dashboard');

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
