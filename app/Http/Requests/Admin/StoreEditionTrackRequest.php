<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class StoreEditionTrackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User && $this->user()->isSuperAdmin();
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_-]+$/'],
            'name_i18n' => ['required', 'array:id,en,ar'],
            'name_i18n.id' => ['required', 'string', 'max:255'],
            'name_i18n.en' => ['required', 'string', 'max:255'],
            'name_i18n.ar' => ['required', 'string', 'max:255'],
            'active' => ['required', 'boolean'],
        ];
    }
}
