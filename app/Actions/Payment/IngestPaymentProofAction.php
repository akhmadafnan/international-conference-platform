<?php

namespace App\Actions\Payment;

use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\StoredFile;
use App\Models\User;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class IngestPaymentProofAction
{
    private const MAX_SIZE_BYTES = 10 * 1024 * 1024;

    /**
     * @var array<string, string>
     */
    private const MIME_EXTENSIONS = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public function __construct(
        private readonly SubmitPaymentProofAction $submitPaymentProof,
    ) {}

    public function handle(
        Payment $payment,
        UploadedFile $file,
        User $actor,
        string $submittedAmount,
        ?string $senderName = null,
        ?CarbonInterface $transferDate = null,
    ): PaymentProof {
        $realPath = $file->getRealPath();

        if ($realPath === false) {
            throw new DomainException(
                'The uploaded payment proof cannot be read.',
            );
        }

        $mimeType = $file->getMimeType();
        $extension = is_string($mimeType)
            ? self::MIME_EXTENSIONS[$mimeType] ?? null
            : null;

        if ($extension === null) {
            throw new DomainException(
                'Payment proof must be a PDF, JPEG, or PNG file.',
            );
        }

        $size = $file->getSize();

        if (! is_int($size) || $size <= 0 || $size > self::MAX_SIZE_BYTES) {
            throw new DomainException(
                'Payment proof file size is invalid.',
            );
        }

        $checksum = hash_file('sha256', $realPath);

        if ($checksum === false) {
            throw new RuntimeException(
                'Unable to calculate payment proof checksum.',
            );
        }

        $path = 'payment-proofs/'.Str::uuid7().'.'.$extension;
        $stream = fopen($realPath, 'rb');

        if ($stream === false) {
            throw new RuntimeException(
                'Unable to open payment proof for protected storage.',
            );
        }

        $storedFile = null;

        try {
            if (! Storage::disk('private')->put($path, $stream)) {
                throw new RuntimeException(
                    'Unable to store payment proof in protected storage.',
                );
            }

            $storedFile = StoredFile::query()->create([
                'disk' => 'private',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mimeType,
                'size_bytes' => $size,
                'checksum_sha256' => $checksum,
                'visibility_class' => 'PRIVATE',
                'uploaded_by_user_id' => $actor->id,
            ]);

            return $this->submitPaymentProof->handle(
                $payment,
                $storedFile,
                $actor,
                $submittedAmount,
                $senderName,
                $transferDate,
            );
        } catch (Throwable $exception) {
            if ($storedFile instanceof StoredFile) {
                $storedFile->delete();
            }

            Storage::disk('private')->delete($path);

            throw $exception;
        } finally {
            fclose($stream);
        }
    }
}
