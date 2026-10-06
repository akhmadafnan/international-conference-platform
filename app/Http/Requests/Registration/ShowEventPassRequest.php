<?php

namespace App\Http\Requests\Registration;

use App\Models\GeneratedDocument;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class ShowEventPassRequest extends FormRequest
{
    public function authorize(): bool
    {
        $document = $this->route('document');
        $user = $this->user();

        if (
            ! $document instanceof GeneratedDocument
            || ! $user instanceof User
            || $document->document_type !== GeneratedDocument::TYPE_EVENT_PASS
            || $document->status !== GeneratedDocument::STATUS_ISSUED
            || $document->revoked_at !== null
        ) {
            return false;
        }

        return $document->recipient_user_id === $user->id
            || $user->isSuperAdmin();
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [];
    }
}
