<?php

namespace App\Http\Controllers\Payment;

use App\Actions\Payment\RequestPaymentCorrectionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\RequestPaymentCorrectionRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use LogicException;
use Inertia\Inertia;

final class RequestPaymentCorrectionController extends Controller
{
    public function __invoke(
        RequestPaymentCorrectionRequest $request,
        Payment $payment,
        RequestPaymentCorrectionAction $action,
    ): RedirectResponse {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new LogicException(
                'Authenticated Finance actor is required.',
            );
        }

        $action->handle(
            $payment,
            $user,
            (string) $request->validated('reason'),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Payment correction requested.'),
        ]);

        return back(303);
    }
}
