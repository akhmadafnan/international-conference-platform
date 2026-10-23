<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('submissions')->select('registration_id')->groupBy('registration_id')
            ->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Cannot add submissions_registration_unique: duplicate registration_id values exist. Resolve legacy drafts explicitly through the UI; no submissions have been changed.');
        }

        Schema::table('submissions', function (Blueprint $table): void {
            $table->unique('registration_id', 'submissions_registration_unique');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table): void {
            $table->dropUnique('submissions_registration_unique');
        });
    }
};
