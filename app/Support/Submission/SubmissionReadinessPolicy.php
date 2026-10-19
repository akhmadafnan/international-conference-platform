<?php

declare(strict_types=1);

namespace App\Support\Submission;

final readonly class SubmissionReadinessPolicy
{
    private function __construct(
        public int $minKeywords,
        public int $maxKeywords,
        public bool $trackRequired,
        public bool $abstractFileRequired,
    ) {}

    /**
     * Read an explicit, Edition-owned policy. An absent/malformed policy never
     * receives permissive defaults and is reported as BLOCKED by the evaluator.
     */
    public static function fromEditionSettings(mixed $settings): ?self
    {
        if (! is_array($settings)) {
            return null;
        }

        $raw = $settings['abstract_submission'] ?? null;

        if (! is_array($raw)
            || ! array_key_exists('min_keywords', $raw)
            || ! array_key_exists('max_keywords', $raw)
            || ! array_key_exists('track_required', $raw)
            || ! array_key_exists('abstract_file_required', $raw)
            || ! is_int($raw['min_keywords'])
            || ! is_int($raw['max_keywords'])
            || $raw['min_keywords'] < 1
            || $raw['max_keywords'] < $raw['min_keywords']
            || ! is_bool($raw['track_required'])
            || ! is_bool($raw['abstract_file_required'])) {
            return null;
        }

        return new self(
            minKeywords: $raw['min_keywords'],
            maxKeywords: $raw['max_keywords'],
            trackRequired: $raw['track_required'],
            abstractFileRequired: $raw['abstract_file_required'],
        );
    }
}
