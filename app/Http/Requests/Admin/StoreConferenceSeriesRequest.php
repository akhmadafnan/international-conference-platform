<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class StoreConferenceSeriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User && $this->user()->isSuperAdmin();
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge(['code' => Str::upper(trim($this->input('code')))]);
        }
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('conference_series', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'acronym' => ['nullable', 'string', 'max:255'],
            'about_i18n' => ['nullable', 'array:id,en,ar'],
            'about_i18n.id' => ['nullable', 'string', 'max:10000'],
            'about_i18n.en' => ['nullable', 'string', 'max:10000'],
            'about_i18n.ar' => ['nullable', 'string', 'max:10000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'required',
            'string' => 'text',
            'code.max' => 'shortText',
            'name.max' => 'shortText',
            'acronym.max' => 'shortText',
            'code.unique' => 'duplicate',
            'about_i18n.array' => 'languages',
            'about_i18n.*.max' => 'longText',
        ];
    }
}
