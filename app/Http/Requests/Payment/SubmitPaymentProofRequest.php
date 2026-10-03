<?php

namespace App\Http\Requests\Payment;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class SubmitPaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        $payment = $this->route('payment');
        $user = $this->user();

        if (! $payment instanceof Payment || ! $user instanceof User) {
            return false;
        }

        return $payment->registration()
            ->whereHas(
                'membership',
                fn ($query) => $query->where('user_id', $user->id),
            )
            ->exists();
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'proof' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],
            'submitted_amount' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'sender_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'transfer_date' => [
                'nullable',
                'date',
            ],
        ];
    }
}
