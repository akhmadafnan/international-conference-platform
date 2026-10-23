<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_screenings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('abstract_version');
            $table->string('outcome', 32);
            $table->text('note');
            $table->foreignUuid('actor_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['submission_id', 'abstract_version']);
        });
        Schema::create('review_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('snapshot_id')->constrained('submission_snapshots')->restrictOnDelete();
            $table->foreignUuid('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('assigned_by')->constrained('users')->restrictOnDelete();
            $table->string('status', 32);
            $table->string('anonymity', 32)->default('SINGLE_ANONYMOUS');
            $table->boolean('same_affiliation')->default(false);
            $table->text('screening_note');
            $table->text('response_note')->nullable();
            $table->timestamps();
            $table->unique(['snapshot_id', 'reviewer_id']);
            $table->index(['reviewer_id', 'status']);
        });
        Schema::create('academic_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assignment_id')->unique()->constrained('review_assignments')->restrictOnDelete();
            $table->string('status', 32);
            $table->string('recommendation', 32)->nullable();
            $table->text('author_comments')->nullable();
            $table->text('confidential_comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
        Schema::create('academic_decisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('abstract_version');
            $table->string('outcome', 32);
            $table->text('author_message');
            $table->text('rationale');
            $table->foreignUuid('actor_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['submission_id', 'abstract_version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_decisions');
        Schema::dropIfExists('academic_reviews');
        Schema::dropIfExists('review_assignments');
        Schema::dropIfExists('academic_screenings');
    }
};
