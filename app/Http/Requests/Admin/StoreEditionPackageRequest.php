<?php

namespace App\Http\Requests\Admin;

use App\Models\ConferenceEdition;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreEditionPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User && $this->user()->isSuperAdmin();
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $edition = $this->route('edition');
        abort_unless($edition instanceof ConferenceEdition, 404);

        return [
            'code' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_-]+$/'],
            'name_i18n' => ['required', 'array:id,en,ar'],
            'name_i18n.id' => ['required', 'string', 'max:255'],
            'name_i18n.en' => ['required', 'string', 'max:255'],
            'name_i18n.ar' => ['required', 'string', 'max:255'],
            'billing_mode' => ['required', Rule::in(['FREE', 'PAID'])],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999999.99', 'decimal:0,2'],
            'currency_code' => ['required', 'string', 'size:3', 'alpha:ascii'],
            'payment_destination_id' => [
                'nullable',
                'uuid',
                Rule::exists('payment_destinations', 'id')
                    ->where('edition_id', $edition->id)
                    ->where('active', true),
            ],
            'active' => ['required', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ];
    }
}
