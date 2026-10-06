<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stored_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('disk');
            $table->string('path')->unique();
            $table->string('original_name');
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('size_bytes');
            $table->char('checksum_sha256', 64);
            $table->string('visibility_class', 32);
            $table->foreignUuid('uploaded_by_user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['visibility_class', 'created_at']);
        });

        Schema::create('conference_series', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('acronym')->nullable();
            $table->json('about_i18n')->nullable();
            $table->foreignUuid('logo_file_id')->nullable()
                ->constrained('stored_files')->nullOnDelete();
            $table->string('status', 32)->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('conference_editions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('series_id')
                ->constrained('conference_series')->restrictOnDelete();
            $table->string('edition_code')->unique();
            $table->unsignedSmallInteger('edition_number')->nullable();
            $table->unsignedSmallInteger('year');
            $table->json('theme_i18n')->nullable();
            $table->string('host_name');
            $table->json('organizer_i18n')->nullable();
            $table->string('mode', 16)->default('OFFLINE');
            $table->string('timezone', 64)->default('UTC');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('registration_opens_at')->nullable();
            $table->timestamp('registration_closes_at')->nullable();
            $table->timestamp('schedule_published_at')->nullable();
            $table->string('lifecycle_status', 32)->default('DRAFT');
            $table->json('settings_json')->nullable();
            $table->timestamps();
            $table->index(['year', 'lifecycle_status']);
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('name');
            $table->text('address');
            $table->string('city');
            $table->char('country_code', 2);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('map_url', 2048)->nullable();
            $table->json('details_i18n')->nullable();
            $table->timestamps();
            $table->index(['edition_id', 'name']);
        });

        Schema::create('important_dates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('code');
            $table->json('label_i18n');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->boolean('public')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            $table->unique(['edition_id', 'code']);
            $table->index(['edition_id', 'public', 'display_order']);
        });

        Schema::create('tracks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('code');
            $table->json('name_i18n');
            $table->json('description_i18n')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['edition_id', 'code']);
            $table->index(['edition_id', 'active']);
        });

        Schema::create('edition_memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->foreignUuid('user_id')
                ->constrained('users')->restrictOnDelete();
            $table->string('membership_status', 32)->default('ACTIVE');
            $table->timestamp('joined_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->unique(['edition_id', 'user_id']);
            $table->index(['edition_id', 'membership_status']);
        });

        Schema::create('payment_destinations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('code');
            $table->string('label');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder');
            $table->json('instructions_i18n')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            $table->unique(['edition_id', 'code']);
            $table->index(['edition_id', 'active', 'is_default']);
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('code');
            $table->json('name_i18n');
            $table->json('description_i18n')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignUuid('venue_id')->nullable()
                ->constrained('venues')->nullOnDelete();
            $table->string('activity_type', 64);
            $table->boolean('attendance_required')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['edition_id', 'code']);
            $table->index(['edition_id', 'active', 'activity_type']);
        });

        Schema::create('participation_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('code');
            $table->json('name_i18n');
            $table->json('description_i18n')->nullable();
            $table->string('billing_mode', 16);
            $table->decimal('price', 14, 2)->default(0);
            $table->char('currency_code', 3)->default('IDR');
            $table->foreignUuid('payment_destination_id')->nullable()
                ->constrained('payment_destinations')->restrictOnDelete();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            $table->unique(['edition_id', 'code']);
            $table->index(['edition_id', 'active', 'display_order']);
        });

        Schema::create('package_activity_entitlements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('package_id')
                ->constrained('participation_packages')->cascadeOnDelete();
            $table->foreignUuid('activity_id')
                ->constrained('activities')->restrictOnDelete();
            $table->string('entitlement_type', 32)->default('INCLUDED');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['package_id', 'activity_id']);
        });

        Schema::create('edition_workflow_windows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('window_code', 64);
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->boolean('active')->default(true);
            $table->json('configuration_json')->nullable();
            $table->timestamps();
            $table->unique(['edition_id', 'window_code']);
            $table->index(['edition_id', 'active']);
        });

        Schema::create('number_sequences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('sequence_type', 64);
            $table->string('prefix')->nullable();
            $table->json('configuration_json')->nullable();
            $table->unsignedBigInteger('last_value')->default(0);
            $table->timestamps();
            $table->unique(['edition_id', 'sequence_type']);
        });

        Schema::create('registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('membership_id')
                ->constrained('edition_memberships')->restrictOnDelete();
            $table->string('registration_code')->unique();
            $table->foreignUuid('package_id')
                ->constrained('participation_packages')->restrictOnDelete();
            $table->string('package_name_snapshot');
            $table->string('participant_category')->nullable();
            $table->string('billing_mode', 16);
            $table->decimal('fee_amount', 14, 2);
            $table->char('currency_code', 3);
            $table->string('status', 32);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->unique('membership_id');
            $table->index(['status', 'created_at']);
        });

        Schema::create('registration_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('registration_id')
                ->constrained('registrations')->restrictOnDelete();
            $table->foreignUuid('activity_id')
                ->constrained('activities')->restrictOnDelete();
            $table->string('entitlement_source', 32);
            $table->string('status', 32);
            $table->text('assigned_notes')->nullable();
            $table->timestamps();
            $table->unique(['registration_id', 'activity_id']);
            $table->index(['registration_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('registration_id')
                ->constrained('registrations')->restrictOnDelete();
            $table->foreignUuid('payment_destination_id')->nullable()
                ->constrained('payment_destinations')->restrictOnDelete();
            $table->uuid('package_id_snapshot')->nullable();
            $table->string('package_name_snapshot');
            $table->decimal('expected_amount', 14, 2);
            $table->char('currency_code', 3);
            $table->json('payment_destination_snapshot_json');
            $table->decimal('submitted_amount', 14, 2)->nullable();
            $table->string('sender_name')->nullable();
            $table->date('transfer_date')->nullable();
            $table->string('status', 32);
            $table->timestamp('submitted_at')->nullable();
            $table->foreignUuid('verified_by_user_id')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('correction_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['registration_id', 'status']);
            $table->index(['status', 'submitted_at']);
        });

        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')
                ->constrained('payments')->restrictOnDelete();
            $table->foreignUuid('stored_file_id')
                ->constrained('stored_files')->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->foreignUuid('submitted_by_user_id')
                ->constrained('users')->restrictOnDelete();
            $table->timestamp('submitted_at');
            $table->timestamp('superseded_at')->nullable();
            $table->timestamps();
            $table->unique(['payment_id', 'version']);
            $table->index(['payment_id', 'superseded_at']);
        });

        Schema::create('registration_fee_exemptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('registration_id')
                ->constrained('registrations')->restrictOnDelete();
            $table->string('reason_code', 64)->nullable();
            $table->text('reason_text');
            $table->foreignUuid('granted_by_user_id')
                ->constrained('users')->restrictOnDelete();
            $table->timestamp('granted_at');
            $table->foreignUuid('revoked_by_user_id')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['registration_id', 'revoked_at']);
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')
                ->constrained('payments')->restrictOnDelete();
            $table->string('reason_code', 64);
            $table->text('reason_text');
            $table->decimal('amount', 14, 2);
            $table->char('currency_code', 3);
            $table->string('status', 32);
            $table->foreignUuid('processed_by_user_id')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->foreignUuid('proof_file_id')->nullable()
                ->constrained('stored_files')->restrictOnDelete();
            $table->json('restricted_destination_json')->nullable();
            $table->timestamps();
            $table->index(['payment_id', 'status']);
        });

        Schema::create('generated_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')
                ->constrained('conference_editions')->restrictOnDelete();
            $table->string('document_type', 64);
            $table->foreignUuid('registration_id')->nullable()
                ->constrained('registrations')->restrictOnDelete();
            $table->uuid('submission_id')->nullable();
            $table->foreignUuid('recipient_user_id')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->string('status', 32);
            $table->string('document_number')->nullable();
            $table->json('snapshot_json');
            $table->foreignUuid('stored_file_id')->nullable()
                ->constrained('stored_files')->restrictOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignUuid('supersedes_document_id')->nullable()
                ->constrained('generated_documents')->restrictOnDelete();
            $table->timestamps();
            $table->index(['edition_id', 'document_type', 'status']);
            $table->index(['registration_id', 'document_type']);
            $table->index(['recipient_user_id', 'document_type']);
        });

        Schema::create('verification_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subject_type');
            $table->uuid('subject_id');
            $table->string('purpose', 64);
            $table->char('token_hash', 64)->unique();
            $table->string('public_code', 64)->nullable()->unique();
            $table->boolean('active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['subject_type', 'subject_id', 'purpose', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_tokens');
        Schema::dropIfExists('generated_documents');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('registration_fee_exemptions');
        Schema::dropIfExists('payment_proofs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('registration_activities');
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('number_sequences');
        Schema::dropIfExists('edition_workflow_windows');
        Schema::dropIfExists('package_activity_entitlements');
        Schema::dropIfExists('participation_packages');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('payment_destinations');
        Schema::dropIfExists('edition_memberships');
        Schema::dropIfExists('tracks');
        Schema::dropIfExists('important_dates');
        Schema::dropIfExists('venues');
        Schema::dropIfExists('conference_editions');
        Schema::dropIfExists('conference_series');
        Schema::dropIfExists('stored_files');
    }
};
