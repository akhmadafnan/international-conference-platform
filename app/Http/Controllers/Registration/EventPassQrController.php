<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\ShowEventPassRequest;
use App\Models\GeneratedDocument;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;

final class EventPassQrController extends Controller
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

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(320, 4),
                new SvgImageBackEnd(),
            ),
        );

        $svg = $writer->writeString((string) $lookup->public_code);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
