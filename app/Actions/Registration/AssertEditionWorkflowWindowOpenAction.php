<?php

namespace App\Actions\Registration;

use App\Enums\WorkflowWindowCode;
use App\Models\ConferenceEdition;
use Carbon\CarbonInterface;
use DomainException;

final class AssertEditionWorkflowWindowOpenAction
{
    public function handle(
        ConferenceEdition $edition,
        WorkflowWindowCode $code,
        ?CarbonInterface $referenceTime = null,
    ): void {
        $window = $edition->workflowWindows()
            ->where('window_code', $code->value)
            ->first();

        if ($window === null || ! $window->active) {
            return;
        }

        $opensAt = $window->opens_at;
        $closesAt = $window->closes_at;

        if (
            $opensAt !== null
            && $closesAt !== null
            && $opensAt->gt($closesAt)
        ) {
            throw new DomainException(sprintf(
                'Active %s workflow window configuration is invalid: opens_at must be earlier than or equal to closes_at.',
                $code->value,
            ));
        }

        $referenceTime ??= now();

        if ($opensAt !== null && $referenceTime->lt($opensAt)) {
            throw new DomainException(sprintf(
                'The %s workflow window is not open yet.',
                $code->value,
            ));
        }

        if ($closesAt !== null && $referenceTime->gt($closesAt)) {
            throw new DomainException(sprintf(
                'The %s workflow window is closed.',
                $code->value,
            ));
        }
    }
}
