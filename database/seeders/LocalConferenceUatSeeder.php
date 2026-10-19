<?php

namespace Database\Seeders;

use App\Actions\Registration\CreateRegistrationIntentAction;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSeries;
use App\Models\ParticipationPackage;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class LocalConferenceUatSeeder extends Seeder
{
    public const EDITION_CODE = 'ICHES-UAT-2027';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) || ! config('demo_roles.enabled')) {
            throw new RuntimeException('Explicit local/testing demo opt-in required.');
        }

        if (trim((string) config('demo_roles.password')) === '') {
            throw new RuntimeException('A strong external demo password must be provided.');
        }

        DB::transaction(function (): void {
            $series = ConferenceSeries::query()->firstOrCreate(
                ['code' => 'ICHES-UAT'],
                ['name' => 'ICHES — LOCAL DEMO ONLY', 'acronym' => 'ICHES-UAT', 'status' => 'ACTIVE'],
            );
            if ($series->name !== 'ICHES — LOCAL DEMO ONLY') {
                throw new RuntimeException('Test-only series code collision.');
            }

            $edition = ConferenceEdition::query()->firstOrCreate(
                ['edition_code' => self::EDITION_CODE],
                [
                    'series_id' => $series->id,
                    'edition_number' => 6,
                    'year' => 2027,
                    'host_name' => 'UNISYA — LOCAL UAT (NOT A REAL EVENT)',
                    'mode' => 'HYBRID',
                    'timezone' => 'Asia/Jakarta',
                    'starts_at' => '2027-11-15 08:00:00',
                    'ends_at' => '2027-11-16 18:00:00',
                    'lifecycle_status' => 'DRAFT',
                    'settings_json' => [
                        'uat_only' => true,
                        'abstract_submission' => [
                            'min_keywords' => 3,
                            'max_keywords' => 5,
                            'track_required' => false,
                            'abstract_file_required' => false,
                        ],
                    ],
                ],
            );
            if ($edition->series_id !== $series->id || ($edition->settings_json['uat_only'] ?? false) !== true) {
                throw new RuntimeException('Test Edition collision or modified test marker.');
            }

            foreach ([
                ['code' => 'PRESENTER-UAT', 'billing_mode' => 'PAID', 'price' => '750000.00',
                    'name_i18n' => ['id' => 'Presenter UAT (SIMULASI)', 'en' => 'UAT Presenter (TEST)', 'ar' => 'مقدم تجريبي']],
                ['code' => 'PARTICIPANT-UAT', 'billing_mode' => 'FREE', 'price' => '0.00',
                    'name_i18n' => ['id' => 'Peserta UAT Gratis (SIMULASI)', 'en' => 'UAT Participant (FREE TEST)', 'ar' => 'مشارك تجريبي']],
            ] as $option) {
                $package = ParticipationPackage::query()->firstOrCreate(
                    ['edition_id' => $edition->id, 'code' => $option['code']],
                    [
                        'billing_mode' => $option['billing_mode'],
                        'price' => $option['price'],
                        'currency_code' => 'IDR',
                        'name_i18n' => $option['name_i18n'],
                        'active' => true,
                        'display_order' => $option['billing_mode'] === 'PAID' ? 1 : 2,
                    ],
                );
                if ($package->edition_id !== $edition->id) {
                    throw new RuntimeException('Test package outside isolated Edition.');
                }
            }

            foreach (['REGISTRATION', 'ABSTRACT_SUBMISSION'] as $code) {
                $edition->workflowWindows()->firstOrCreate(
                    ['window_code' => $code],
                    [
                        'opens_at' => now()->subDay(),
                        'closes_at' => now()->addDays(21),
                        'active' => true,
                        'configuration_json' => ['uat_only' => true],
                    ],
                );
            }
        });

        $previousCode = config('demo_roles.edition_code');
        try {
            config(['demo_roles.edition_code' => self::EDITION_CODE]);
            $this->call(EditionPersonaDemoSeeder::class);
        } finally {
            config(['demo_roles.edition_code' => $previousCode]);
        }

        $edition = ConferenceEdition::query()->where('edition_code', self::EDITION_CODE)->sole();
        $package = $edition->participationPackages()->where('code', 'PRESENTER-UAT')->sole();

        // Registration intent is created using the actual Phase02 domain action.
        // The demo author creates their own abstract DRAFT through the HTTP wizard.
        $actor = User::query()->where('email', 'demo+author.iches-uat-2027@example.test')->sole();

        if (! Registration::query()->whereHas('membership', fn ($query) => $query
            ->where('edition_id', $edition->id)->where('user_id', $actor->id))->exists()) {
            app(CreateRegistrationIntentAction::class)->handle($actor, $edition, $package);
        }
    }
}
