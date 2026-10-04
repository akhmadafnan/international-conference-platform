<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\ShowEventPassRequest;
use App\Models\GeneratedDocument;
use Inertia\Inertia;
use Inertia\Response;

final class ShowEventPassController extends Controller
{
    public function __invoke(
        ShowEventPassRequest $request,
        GeneratedDocument $document,
    ): Response {
        $lookup = $document->verificationTokens()
            ->where('purpose', 'EVENT_PASS_LOOKUP')
            ->where('active', true)
            ->whereNull('revoked_at')
            ->firstOrFail();

        return Inertia::render('documents/EventPass', [
            'eventPass' => [
                'id' => $document->id,
                'issuedAt' => $document->issued_at?->toIso8601String(),
                'snapshot' => $document->snapshot_json,
                'publicCode' => $lookup->public_code,
                'qrUrl' => route('documents.event-pass.qr', $document),
            ],
            'routes' => [
                'dashboard' => route('dashboard'),
            ],
        ]);
    }
}
