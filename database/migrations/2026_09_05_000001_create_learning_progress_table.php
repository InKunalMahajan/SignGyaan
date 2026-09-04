<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject_slug', 100);
            $table->string('subject_name', 120);
            $table->string('course_slug', 140);
            $table->string('course_title', 180);
            $table->unsignedInteger('total_lessons')->default(1);
            $table->string('current_lesson_key', 120)->default('unit-1-lesson-1');
            $table->json('completed_lessons')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'subject_slug', 'course_slug'], 'learning_progress_user_course_unique');
            $table->index(['user_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_progress');
    }
};
