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
                            'track_required' => true,
                            'abstract_file_required' => false,
                        ],
                    ],
                ],
            );
            if ($edition->series_id !== $series->id || ($edition->settings_json['uat_only'] ?? false) !== true) {
                throw new RuntimeException('Test Edition collision or modified test marker.');
            }

            // UAT fixture only. Existing operator-edited Topics are never overwritten.
            foreach ([
                'T01' => ['en' => 'Islamic Studies, Ethics, and Contemporary Civilization', 'id' => 'Studi Islam, Etika, dan Peradaban Kontemporer', 'ar' => 'الدراسات الإسلامية والأخلاق والحضارة المعاصرة'],
                'T02' => ['en' => 'Education, Pedagogy, and Human Development', 'id' => 'Pendidikan, Pedagogi, dan Pengembangan Manusia', 'ar' => 'التعليم وطرائق التدريس والتنمية البشرية'],
                'T03' => ['en' => 'Leadership, Governance, and Institutional Accountability', 'id' => 'Kepemimpinan, Tata Kelola, dan Akuntabilitas Kelembagaan', 'ar' => 'القيادة والحوكمة والمساءلة المؤسسية'],
                'T04' => ['en' => 'Economics, Sustainable Business, and Inclusive Development', 'id' => 'Ekonomi, Bisnis Berkelanjutan, dan Pembangunan Inklusif', 'ar' => 'الاقتصاد والأعمال المستدامة والتنمية الشاملة'],
                'T05' => ['en' => 'Law, Human Rights, Social Justice, and Public Policy', 'id' => 'Hukum, Hak Asasi Manusia, Keadilan Sosial, dan Kebijakan Publik', 'ar' => 'القانون وحقوق الإنسان والعدالة الاجتماعية والسياسات العامة'],
                'T06' => ['en' => 'Social Sciences, Humanities, Culture, and Community Resilience', 'id' => 'Ilmu Sosial, Humaniora, Budaya, dan Ketahanan Masyarakat', 'ar' => 'العلوم الاجتماعية والإنسانية والثقافة وصمود المجتمع'],
                'T07' => ['en' => 'Digital Transformation, Responsible Innovation, and Society', 'id' => 'Transformasi Digital, Inovasi Bertanggung Jawab, dan Masyarakat', 'ar' => 'التحول الرقمي والابتكار المسؤول والمجتمع'],
                'T08' => ['en' => 'Environmental Sustainability, and Human Well-Being', 'id' => 'Keberlanjutan Lingkungan dan Kesejahteraan Manusia', 'ar' => 'الاستدامة البيئية ورفاه الإنسان'],
            ] as $code => $names) {
                $edition->tracks()->firstOrCreate(['code' => $code], ['name_i18n' => $names, 'active' => true]);
            }
            $settings = $edition->settings_json;
            $settings['abstract_submission']['track_required'] = true;
            $edition->settings_json = $settings;
            if ($edition->isDirty()) {
                $edition->save();
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
            $actor = User::query()->where('email', 'author@iches.test')->sole();

            if (! Registration::query()->whereHas('membership', fn ($query) => $query
                ->where('edition_id', $edition->id)->where('user_id', $actor->id))->exists()) {
                app(CreateRegistrationIntentAction::class)->handle($actor, $edition, $package);
            }
        });
    }
}
