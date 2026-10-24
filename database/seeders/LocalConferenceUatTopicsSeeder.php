<?php

namespace Database\Seeders;

use App\Models\ConferenceEdition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class LocalConferenceUatTopicsSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) || ! config('demo_roles.enabled')) {
            throw new RuntimeException('Explicit local/testing demo opt-in required.');
        }

        DB::transaction(function (): void {
            $edition = ConferenceEdition::query()
                ->where('edition_code', LocalConferenceUatSeeder::EDITION_CODE)
                ->lockForUpdate()
                ->first();

            if ($edition === null || ($edition->settings_json['uat_only'] ?? false) !== true) {
                throw new RuntimeException('Existing ICHES-UAT-2027 Edition with uat_only marker required.');
            }

            foreach (LocalConferenceUatSeeder::TOPICS as $code => $names) {
                $edition->tracks()->firstOrCreate(
                    ['code' => $code],
                    ['name_i18n' => $names, 'active' => true],
                );
            }
        });
    }
}
