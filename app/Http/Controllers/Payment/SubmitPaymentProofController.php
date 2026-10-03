<?php

namespace App\Http\Controllers\Payment;

use App\Actions\Payment\IngestPaymentProofAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\SubmitPaymentProofRequest;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use LogicException;
use Inertia\Inertia;

final class SubmitPaymentProofController extends Controller
{
    public function __invoke(
        SubmitPaymentProofRequest $request,
        Payment $payment,
        IngestPaymentProofAction $action,
    ): RedirectResponse {
        $user = $request->user();
        $file = $request->file('proof');

        if (! $user instanceof User || ! $file instanceof UploadedFile) {
            throw new LogicException(
                'Validated payment proof request is missing its user or file.',
            );
        }

        $transferDate = $request->validated('transfer_date');

        $action->handle(
            $payment,
            $file,
            $user,
            (string) $request->validated('submitted_amount'),
            $request->validated('sender_name'),
            is_string($transferDate)
                ? CarbonImmutable::parse($transferDate)
                : null,
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Payment proof submitted.'),
        ]);

        return back(303);
    }
}
