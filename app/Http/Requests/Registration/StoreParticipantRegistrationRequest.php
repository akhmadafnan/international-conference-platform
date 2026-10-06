<?php

namespace App\Http\Requests\Registration;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class StoreParticipantRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'package_id' => [
                'required',
                'uuid',
                'exists:participation_packages,id',
            ],
            'participant_category' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'package_id.required' => 'PACKAGE_INVALID',
            'package_id.uuid' => 'PACKAGE_INVALID',
            'package_id.exists' => 'PACKAGE_INVALID',
        ];
    }
}
