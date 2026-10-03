<?php

namespace App\Http\Requests\Payment;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class RequestPaymentCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof User
            && $user->can('payment.verify');
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ];
    }
}
