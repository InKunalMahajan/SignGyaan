<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('term', 180);
            $table->string('slug', 200)->unique();
            $table->text('meaning')->nullable();
            $table->text('example')->nullable();
            $table->foreignId('isl_media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->string('isl_video_url', 2048)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['subject_id', 'is_published']);
            $table->index(['course_id', 'is_published']);
            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('lesson_vocabulary_term', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vocabulary_term_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['lesson_id', 'vocabulary_term_id']);
            $table->index(['lesson_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_vocabulary_term');
        Schema::dropIfExists('vocabulary_terms');
    }
};
