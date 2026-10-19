<?php

namespace App\Http\Requests\Admin;

use App\Enums\WorkflowWindowCode;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreEditionWindowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User && $this->user()->isSuperAdmin();
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'window_code' => ['required', Rule::enum(WorkflowWindowCode::class)],
            'opens_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'closes_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'active' => ['required', 'boolean'],
        ];
    }
}
