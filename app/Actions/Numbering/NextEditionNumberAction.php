<?php

namespace App\Actions\Numbering;

use App\Models\ConferenceEdition;
use App\Models\NumberSequence;
use Illuminate\Support\Facades\DB;

class NextEditionNumberAction
{
    public function handle(
        ConferenceEdition $edition,
        string $sequenceType,
        string $prefix,
        int $padding = 4,
    ): string {
        return DB::transaction(function () use ($edition, $sequenceType, $prefix, $padding): string {
            NumberSequence::query()->firstOrCreate(
                [
                    'edition_id' => $edition->id,
                    'sequence_type' => $sequenceType,
                ],
                [
                    'prefix' => $prefix,
                    'last_value' => 0,
                ],
            );

            $sequence = NumberSequence::query()
                ->where('edition_id', $edition->id)
                ->where('sequence_type', $sequenceType)
                ->lockForUpdate()
                ->firstOrFail();

            $sequence->last_value++;
            $sequence->prefix = $prefix;
            $sequence->save();

            return $prefix.str_pad(
                (string) $sequence->last_value,
                $padding,
                '0',
                STR_PAD_LEFT,
            );
        });
    }
}
