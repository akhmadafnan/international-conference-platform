<?php

namespace App\Support\Scholarly;

use DomainException;

final class DoiNormalizer
{
    /**
     * Maximum stored length of a canonical bare DOI, mirroring the
     * `submission_references.doi` string column length.
     */
    public const MAX_LENGTH = 255;

    private const PREFIX_PATTERN = '/^doi:\s*(.+)$/i';

    private const RESOLVER_PATTERN = '~^(?:https?://)?(?:www\.)?(?:dx\.)?doi\.org/(.+)$~i';

    private const DOI_PATTERN = '/^10\.\d{4,9}\/[^\s?#]+$/D';

    /**
     * Normalize a supplied DOI to its canonical bare form.
     *
     * Returns null when no DOI is supplied. Accepts the conventional bare DOI,
     * the `doi:` prefix form, and unambiguous doi.org / dx.doi.org resolver URLs.
     * A supplied value that is not a valid DOI throws a DomainException; the DOI
     * is never looked up, invented, or resolved over the network.
     *
     * DOI names are case-insensitive for resolution, so the registrant suffix is
     * stored exactly as supplied.
     */
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $candidate = self::extractBareDoi($value);

        if (preg_match(self::DOI_PATTERN, $candidate) !== 1) {
            throw new DomainException(
                'The supplied DOI is not a valid DOI.',
            );
        }

        if (strlen($candidate) > self::MAX_LENGTH) {
            throw new DomainException(
                'The supplied DOI exceeds the maximum storable length.',
            );
        }

        return $candidate;
    }

    private static function extractBareDoi(string $value): string
    {
        if (preg_match(self::PREFIX_PATTERN, $value, $matches) === 1) {
            return trim($matches[1]);
        }

        if (preg_match(self::RESOLVER_PATTERN, $value, $matches) === 1) {
            return trim($matches[1]);
        }

        return $value;
    }
}
