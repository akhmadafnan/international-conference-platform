<?php

declare(strict_types=1);

namespace App\Support\Submission;

/**
 * A side-effect-free advisory result. Callers must never use it as a durable
 * permission or as a substitute for P03-H transactional revalidation.
 */
final readonly class SubmissionReadinessResult
{
    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    private function __construct(
        public string $status,
        public array $findings,
    ) {}

    /**
     * @param  list<array{code: string, severity: string, field: string, context: array<string, int>}>  $findings
     */
    public static function fromFindings(array $findings): self
    {
        $unique = [];

        foreach ($findings as $finding) {
            // Canonical, non-PII deduplication key.
            $key = $finding['code'].'|'.$finding['field'].'|'.json_encode($finding['context']);
            $unique[$key] = $finding;
        }

        $sorted = array_values($unique);
        usort($sorted, static function (array $left, array $right): int {
            $rank = static fn (string $severity): int => $severity === 'BLOCKED' ? 0 : 1;

            return [$rank($left['severity']), $left['field'], $left['code']]
                <=> [$rank($right['severity']), $right['field'], $right['code']];
        });

        $blocked = false;
        foreach ($sorted as $finding) {
            if ($finding['severity'] === 'BLOCKED') {
                $blocked = true;
                break;
            }
        }

        return new self($blocked ? 'BLOCKED' : ($sorted !== [] ? 'WARNING' : 'READY'), $sorted);
    }

    /**
     * @return array{status: string, findings: list<array{code: string, severity: string, field: string, context: array<string, int>}>}
     */
    public function toArray(): array
    {
        return ['status' => $this->status, 'findings' => $this->findings];
    }
}
