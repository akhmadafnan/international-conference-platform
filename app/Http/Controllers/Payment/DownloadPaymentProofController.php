<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\DownloadPaymentProofRequest;
use App\Models\Payment;
use App\Models\PaymentProof;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadPaymentProofController extends Controller
{
    public function __invoke(
        DownloadPaymentProofRequest $request,
        Payment $payment,
        PaymentProof $proof,
    ): StreamedResponse {
        $proof->loadMissing('storedFile');
        $storedFile = $proof->storedFile;

        abort_if(
            $proof->payment_id !== $payment->id
                || $storedFile === null
                || $storedFile->disk !== 'private'
                || strtoupper($storedFile->visibility_class) === 'PUBLIC'
                || ! Storage::disk('private')->exists($storedFile->path),
            404,
        );

        return Storage::disk('private')->download(
            $storedFile->path,
            $storedFile->original_name,
            ['Content-Type' => $storedFile->mime_type],
        );
    }
}
