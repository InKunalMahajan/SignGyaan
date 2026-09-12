<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assessments')) {
            Schema::create('assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->restrictOnDelete();
                $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
                $table->string('title');
                $table->text('instructions')->nullable();
                $table->string('status', 24)->default('draft');
                $table->decimal('total_marks', 8, 2)->default(0);
                $table->decimal('passing_marks', 8, 2)->nullable();
                $table->unsignedSmallInteger('duration_minutes')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->index(['course_id', 'status']);
                $table->index(['created_by', 'status']);
            });
        }

        if (! Schema::hasTable('assessment_questions')) {
            Schema::create('assessment_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
                $table->string('type', 32);
                $table->text('prompt');
                $table->decimal('marks', 8, 2)->default(1);
                $table->unsignedInteger('position')->default(1);
                $table->json('config')->nullable();
                $table->json('answer_key')->nullable();
                $table->text('explanation')->nullable();
                $table->timestamps();

                $table->unique(['assessment_id', 'position']);
                $table->index(['assessment_id', 'type']);
            });
        }

        if (! Schema::hasTable('assessment_attempts')) {
            Schema::create('assessment_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_id')->constrained()->restrictOnDelete();
                $table->foreignId('learner_id')->constrained('users')->restrictOnDelete();
                $table->unsignedInteger('attempt_number')->default(1);
                $table->string('status', 24)->default('in_progress');
                $table->decimal('earned_marks', 8, 2)->nullable();
                $table->decimal('total_marks_snapshot', 8, 2);
                $table->decimal('passing_marks_snapshot', 8, 2)->nullable();
                $table->decimal('percentage', 6, 2)->nullable();
                $table->boolean('passed')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();

                $table->unique(['assessment_id', 'learner_id', 'attempt_number'], 'assessment_attempt_unique');
                $table->index(['learner_id', 'status']);
                $table->index(['assessment_id', 'status']);
            });
        }

        if (! Schema::hasTable('assessment_answers')) {
            Schema::create('assessment_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attempt_id')->constrained('assessment_attempts')->cascadeOnDelete();
                $table->foreignId('question_id')->constrained('assessment_questions')->restrictOnDelete();
                $table->json('response')->nullable();
                $table->json('question_snapshot')->nullable();
                $table->decimal('awarded_marks', 8, 2)->nullable();
                $table->boolean('is_correct')->nullable();
                $table->boolean('requires_review')->default(false);
                $table->text('feedback')->nullable();
                $table->timestamps();

                $table->unique(['attempt_id', 'question_id']);
                $table->index(['attempt_id', 'requires_review']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_attempts');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
    }
};
