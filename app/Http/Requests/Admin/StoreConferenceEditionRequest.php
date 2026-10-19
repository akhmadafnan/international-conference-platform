<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class StoreConferenceEditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User && $this->user()->isSuperAdmin();
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('edition_code'))) {
            $this->merge(['edition_code' => Str::upper(trim($this->input('edition_code')))]);
        }

        if (! $this->exists('timezone')) {
            $this->merge(['timezone' => 'Asia/Jakarta']);
        }
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'series_id' => ['required', 'uuid', Rule::exists('conference_series', 'id')->where('status', 'ACTIVE')],
            'edition_code' => ['required', 'string', 'max:255', Rule::unique('conference_editions', 'edition_code')],
            'edition_number' => ['nullable', 'integer', 'between:1,65535'],
            'year' => ['required', 'integer', 'between:1,9999'],
            'host_name' => ['required', 'string', 'max:255'],
            'mode' => ['required', Rule::in(['OFFLINE', 'ONLINE', 'HYBRID'])],
            'timezone' => ['required', 'string', 'max:64', 'timezone:all'],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'ends_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'theme_i18n' => ['nullable', 'array:id,en,ar'],
            'theme_i18n.*' => ['nullable', 'string', 'max:10000'],
            'organizer_i18n' => ['nullable', 'array:id,en,ar'],
            'organizer_i18n.*' => ['nullable', 'string', 'max:10000'],
        ];
    }

    /** @return list<\Closure> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $allowed = array_merge(array_keys($this->rules()), ['_token']);
            foreach (array_keys($this->all()) as $field) {
                if (! in_array($field, $allowed, true)) {
                    $validator->errors()->add($field, 'unexpected');
                }
            }

            if ($validator->errors()->hasAny(['timezone', 'starts_at', 'ends_at'])) {
                return;
            }

            $dates = [];
            foreach (['starts_at', 'ends_at'] as $field) {
                $date = CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $this->input($field), $this->input('timezone'));
                // Reject wall-clock times skipped by a daylight-saving transition.
                if (! $date || $date->format('Y-m-d\TH:i') !== $this->input($field)) {
                    $validator->errors()->add($field, 'date');

                    continue;
                }
                $dates[$field] = $date;
            }

            if (isset($dates['starts_at'], $dates['ends_at']) && $dates['ends_at']->lessThanOrEqualTo($dates['starts_at'])) {
                $validator->errors()->add('ends_at', 'dateRange');
            }
        }];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => 'required',
            'string' => 'text',
            'max' => 'tooLong',
            'integer' => 'integer',
            'between' => 'range',
            'series_id.uuid' => 'series',
            'series_id.exists' => 'series',
            'edition_code.unique' => 'duplicate',
            'mode.in' => 'mode',
            'timezone.timezone' => 'timezone',
            'date_format' => 'date',
            'array' => 'languages',
        ];
    }
}
