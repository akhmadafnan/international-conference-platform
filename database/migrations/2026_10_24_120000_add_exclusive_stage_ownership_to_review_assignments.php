<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('review_assignments')->whereIn('status', ['INVITED', 'ACCEPTED'])
            ->groupBy('submission_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Conflicting legacy abstract owners require human resolution before migration.');
        }

        Schema::table('review_assignments', function (Blueprint $table): void {
            $table->string('academic_stage', 32)->default('ABSTRACT');
            $table->string('assignment_source', 32)->default('MANUAL');
            $table->unsignedTinyInteger('active_owner_slot')->nullable()
                ->virtualAs("CASE WHEN status IN ('INVITED', 'ACCEPTED') THEN 1 ELSE NULL END");
            $table->unique(['submission_id', 'academic_stage', 'active_owner_slot'], 'review_assignment_stage_owner_unique');
            $table->unique(['snapshot_id', 'reviewer_id', 'academic_stage'], 'review_assignment_snapshot_reviewer_stage_unique');
            $table->dropUnique(['snapshot_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        if (DB::table('review_assignments')->groupBy('snapshot_id', 'reviewer_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Stage-specific history cannot be collapsed into the legacy unique key.');
        }
        Schema::table('review_assignments', function (Blueprint $table): void {
            $table->dropUnique('review_assignment_stage_owner_unique');
            $table->unique(['snapshot_id', 'reviewer_id']);
            $table->dropUnique('review_assignment_snapshot_reviewer_stage_unique');
            $table->dropColumn(['active_owner_slot', 'academic_stage', 'assignment_source']);
        });
    }
};
