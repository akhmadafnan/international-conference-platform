<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('preferred_name');
            $table->string('ror_uri')->nullable()->unique();
            $table->char('country_code', 2)->nullable();
            $table->string('website')->nullable();
            $table->json('aliases_json')->nullable();
            $table->string('source', 32);
            $table->timestamps();
            $table->index(['preferred_name']);
            $table->index(['ror_uri']);
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->foreignUuid('registration_id')
                ->constrained('registrations')->restrictOnDelete();
            $table->string('paper_code');
            $table->foreignUuid('track_id')->nullable()
                ->constrained('tracks')->nullOnDelete();
            $table->char('primary_locale', 2);
            $table->string('academic_status', 32)->default('DRAFT');
            $table->unsignedInteger('current_abstract_version')->default(0);
            $table->foreignUuid('actual_presenter_contributor_id')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('final_academic_approved_at')->nullable();
            $table->timestamps();
            $table->unique(['edition_id', 'paper_code']);
            $table->index(['edition_id', 'academic_status']);
            $table->index(['registration_id', 'academic_status']);
        });

        Schema::create('submission_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')
                ->constrained('submissions')->cascadeOnDelete();
            $table->char('locale', 2);
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('abstract_text');
            $table->timestamps();
            $table->unique(['submission_id', 'locale']);
            $table->index(['submission_id']);
        });

        Schema::create('submission_keywords', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')
                ->constrained('submissions')->cascadeOnDelete();
            $table->char('locale', 2);
            $table->string('value');
            $table->unsignedInteger('sequence');
            $table->timestamps();
            $table->unique(['submission_id', 'locale', 'sequence']);
            $table->index(['submission_id', 'locale']);
        });

        Schema::create('submission_contributors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')
                ->constrained('submissions')->cascadeOnDelete();
            $table->foreignUuid('linked_user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->string('display_name');
            $table->string('given_name')->nullable();
            $table->string('family_name')->nullable();
            $table->boolean('single_name')->default(false);
            $table->string('email');
            $table->char('country_code', 2);
            $table->string('orcid_uri')->nullable();
            $table->string('orcid_verification_state', 32)->nullable();
            $table->boolean('is_corresponding')->default(false);
            $table->unsignedInteger('sequence');
            $table->string('contributor_role', 32)->default('AUTHOR');
            $table->timestamps();
            $table->unique(['submission_id', 'sequence']);
            $table->index(['submission_id']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->foreign('actual_presenter_contributor_id')
                ->references('id')
                ->on('submission_contributors')
                ->nullOnDelete();
        });

        Schema::create('contributor_affiliations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_contributor_id')
                ->constrained('submission_contributors')->cascadeOnDelete();
            $table->foreignUuid('institution_id')->nullable()
                ->constrained('institutions')->nullOnDelete();
            $table->string('institution_name_snapshot');
            $table->string('ror_uri_snapshot')->nullable();
            $table->text('subdivision_text')->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('city_text')->nullable();
            $table->unsignedInteger('sequence');
            $table->timestamps();
            $table->unique(['submission_contributor_id', 'sequence'], 'contrib_affil_seq_unique');
            $table->index(['submission_contributor_id']);
        });

        Schema::create('submission_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')
                ->constrained('submissions')->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->text('raw_citation');
            $table->string('doi')->nullable();
            $table->string('url')->nullable();
            $table->json('structured_json')->nullable();
            $table->timestamps();
            $table->unique(['submission_id', 'sequence']);
            $table->index(['submission_id']);
        });

        Schema::create('submission_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')
                ->constrained('submissions')->cascadeOnDelete();
            $table->foreignUuid('stored_file_id')
                ->constrained('stored_files')->restrictOnDelete();
            $table->string('file_role', 64);
            $table->unsignedInteger('manuscript_version')->nullable();
            $table->foreignUuid('uploaded_by_user_id')
                ->constrained('users')->restrictOnDelete();
            $table->timestamp('submitted_at');
            $table->foreignUuid('supersedes_submission_file_id')->nullable()
                ->constrained('submission_files')->nullOnDelete();
            $table->string('status', 32)->default('ACTIVE');
            $table->timestamps();
            $table->index(['submission_id', 'file_role']);
            $table->index(['submission_id', 'status']);
            $table->index(['stored_file_id']);
            $table->index(['supersedes_submission_file_id']);
        });

        Schema::create('submission_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')
                ->constrained('submissions')->cascadeOnDelete();
            $table->string('snapshot_type', 64);
            $table->unsignedInteger('version');
            $table->json('metadata_json');
            $table->char('checksum', 64);
            $table->foreignUuid('created_by_user_id')
                ->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['submission_id', 'snapshot_type', 'version']);
            $table->index(['submission_id']);
        });

        Schema::table('generated_documents', function (Blueprint $table) {
            $table->foreign('submission_id')
                ->references('id')
                ->on('submissions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('generated_documents', function (Blueprint $table) {
            $table->dropForeign(['submission_id']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['actual_presenter_contributor_id']);
        });

        Schema::dropIfExists('submission_snapshots');
        Schema::dropIfExists('submission_files');
        Schema::dropIfExists('submission_references');
        Schema::dropIfExists('contributor_affiliations');
        Schema::dropIfExists('submission_contributors');
        Schema::dropIfExists('submission_keywords');
        Schema::dropIfExists('submission_translations');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('institutions');
    }
};
