<?php

namespace App\Support\Scholarly;

use DomainException;

final class OrcidNormalizer
{
    private const COMPACT_PATTERN = '/^[0-9]{15}[0-9X]$/';

    private const URL_PATTERN = '~^(?:https?://)?(?:www\.)?orcid\.org/([^/?#]+)~i';

    /**
     * Normalize a supplied ORCID value to its canonical URI form.
     *
     * Returns null when no ORCID is supplied. Throws a DomainException when a
     * supplied value is not a structurally valid ORCID iD with a valid MOD 11-2
     * checksum.
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

        $compact = self::extractCompactForm($value);

        if (preg_match(self::COMPACT_PATTERN, $compact) !== 1) {
            throw new DomainException(
                'The supplied ORCID is not a valid ORCID iD.',
            );
        }

        if (! self::hasValidChecksum($compact)) {
            throw new DomainException(
                'The supplied ORCID is not a valid ORCID iD.',
            );
        }

        return 'https://orcid.org/'.self::toDashedForm($compact);
    }

    private static function extractCompactForm(string $value): string
    {
        if (preg_match(self::URL_PATTERN, $value, $matches) === 1) {
            $value = $matches[1];
        }

        return strtoupper(str_replace('-', '', $value));
    }

    private static function hasValidChecksum(string $digits): bool
    {
        $total = 0;

        for ($position = 0; $position < 15; $position++) {
            $total = ($total + (int) $digits[$position]) * 2;
        }

        $expectedValue = (12 - ($total % 11)) % 11;
        $expectedDigit = $expectedValue === 10 ? 'X' : (string) $expectedValue;

        return $digits[15] === $expectedDigit;
    }

    private static function toDashedForm(string $digits): string
    {
        return substr($digits, 0, 4)
            .'-'.substr($digits, 4, 4)
            .'-'.substr($digits, 8, 4)
            .'-'.substr($digits, 12, 4);
    }
}
