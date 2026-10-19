<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class DeleteConferenceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User && $this->user()->isSuperAdmin();
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return ['confirmation_code' => ['required', 'string', 'max:255']];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'confirmation_code.required' => 'confirmation',
            'confirmation_code.string' => 'confirmation',
            'confirmation_code.max' => 'confirmation',
        ];
    }
}
