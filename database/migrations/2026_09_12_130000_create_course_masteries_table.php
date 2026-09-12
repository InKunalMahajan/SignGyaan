<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_masteries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('learner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('lessons_completed')->default(0);
            $table->unsignedInteger('lessons_total')->default(0);
            $table->decimal('lesson_completion_percentage', 5, 2)->default(0);
            $table->unsignedInteger('assessments_completed')->default(0);
            $table->decimal('assessment_percentage', 5, 2)->nullable();
            $table->decimal('mastery_score', 5, 2)->default(0);
            $table->string('mastery_level', 32)->default('needs_support');
            $table->string('evidence_status', 32)->default('learning_only');
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['learner_id', 'course_id']);
            $table->index(['course_id', 'mastery_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_masteries');
    }
};
