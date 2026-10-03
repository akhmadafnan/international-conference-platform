<?php

namespace App\Http\Requests\Payment;

use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\User;
use App\Support\Authorization\ActiveConferenceEditionContext;
use Illuminate\Foundation\Http\FormRequest;

final class DownloadPaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        $payment = $this->route('payment');
        $proof = $this->route('proof');
        $user = $this->user();

        if (
            ! $payment instanceof Payment
            || ! $proof instanceof PaymentProof
            || ! $user instanceof User
            || $proof->payment_id !== $payment->id
        ) {
            return false;
        }

        $isOwner = $payment->registration()
            ->whereHas(
                'membership',
                fn ($query) => $query->where('user_id', $user->id),
            )
            ->exists();

        if ($isOwner || $user->isSuperAdmin()) {
            return true;
        }

        $registration = $payment->registration()->first();
        $editionId = $registration?->membership()
            ->value('edition_id');

        if (! is_string($editionId)) {
            return false;
        }

        $context = app(ActiveConferenceEditionContext::class);

        return $context->id() === $editionId
            && getPermissionsTeamId() === $editionId
            && $user->can('payment.verify');
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [];
    }
}
