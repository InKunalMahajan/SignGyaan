<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_resource_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('passing_percentage')->default(70);
            $table->unsignedSmallInteger('max_attempts')->nullable();
            $table->unsignedSmallInteger('time_limit_minutes')->nullable();
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->boolean('show_feedback')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['is_published', 'practice_resource_id']);
        });

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('question_type', 40)->default('single-choice');
            $table->text('prompt');
            $table->text('explanation')->nullable();
            $table->json('answer_key')->nullable();
            $table->decimal('points', 8, 2)->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['assessment_id', 'is_published', 'sort_order'], 'assessment_questions_order_index');
        });

        Schema::create('assessment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_question_id')->constrained()->cascadeOnDelete();
            $table->text('option_text');
            $table->text('feedback')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['assessment_question_id', 'sort_order'], 'assessment_options_order_index');
        });

        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('attempt_number')->default(1);
            $table->string('status', 30)->default('in-progress');
            $table->decimal('score_points', 8, 2)->default(0);
            $table->decimal('max_points', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('passed')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['assessment_id', 'user_id', 'attempt_number'], 'assessment_attempt_number_unique');
            $table->index(['user_id', 'status', 'updated_at'], 'assessment_attempt_user_status_index');
        });

        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_question_id')->constrained()->cascadeOnDelete();
            $table->json('response')->nullable();
            $table->text('question_snapshot')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_awarded', 8, 2)->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['assessment_attempt_id', 'assessment_question_id'], 'assessment_answer_attempt_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_attempts');
        Schema::dropIfExists('assessment_options');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
    }
};
