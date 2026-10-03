<?php

namespace App\Http\Controllers\Payment;

use App\Actions\Payment\VerifyPaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\VerifyPaymentRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use LogicException;
use Inertia\Inertia;

final class VerifyPaymentController extends Controller
{
    public function __invoke(
        VerifyPaymentRequest $request,
        Payment $payment,
        VerifyPaymentAction $action,
    ): RedirectResponse {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new LogicException(
                'Authenticated Finance actor is required.',
            );
        }

        $action->handle($payment, $user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Payment verified.'),
        ]);

        return back(303);
    }
}
