<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviewer_topic_eligibilities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('edition_id')->constrained('conference_editions')->restrictOnDelete();
            $table->foreignUuid('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('track_id')->constrained('tracks')->restrictOnDelete();
            $table->foreignUuid('assigned_by')->constrained('users')->restrictOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['edition_id', 'reviewer_id', 'track_id'], 'reviewer_topic_eligibility_unique');
            $table->index(['reviewer_id', 'edition_id', 'active'], 'reviewer_topic_active_index');
            $table->index(['edition_id', 'track_id', 'active'], 'edition_topic_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviewer_topic_eligibilities');
    }
};
