<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class StoreAbstractSubmissionPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User && $this->user()->isSuperAdmin();
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'min_keywords' => ['required', 'integer', 'min:1', 'max:20'],
            'max_keywords' => ['required', 'integer', 'gte:min_keywords', 'max:20'],
            'track_required' => ['required', 'boolean'],
            'abstract_file_required' => ['required', 'boolean'],
        ];
    }
}
