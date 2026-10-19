<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class StoreEditionDestinationRequest extends FormRequest
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
            'label' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'account_holder' => ['required', 'string', 'max:255'],
            'is_default' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ];
    }
}
