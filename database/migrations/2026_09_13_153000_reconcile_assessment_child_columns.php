<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->reconcileQuestions();
        $this->reconcileAttempts();
        $this->reconcileAnswers();
    }

    private function reconcileQuestions(): void
    {
        if (! Schema::hasTable('assessment_questions')) {
            return;
        }

        $columns = [
            'assessment_id' => fn (Blueprint $table) => $table->unsignedBigInteger('assessment_id')->nullable(),
            'type' => fn (Blueprint $table) => $table->string('type', 32)->nullable(),
            'prompt' => fn (Blueprint $table) => $table->text('prompt')->nullable(),
            'marks' => fn (Blueprint $table) => $table->decimal('marks', 8, 2)->default(1),
            'position' => fn (Blueprint $table) => $table->unsignedInteger('position')->default(1),
            'config' => fn (Blueprint $table) => $table->json('config')->nullable(),
            'answer_key' => fn (Blueprint $table) => $table->json('answer_key')->nullable(),
            'explanation' => fn (Blueprint $table) => $table->text('explanation')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('assessment_questions', $column)) {
                Schema::table('assessment_questions', function (Blueprint $table) use ($definition): void {
                    $definition($table);
                });
            }
        }
    }

    private function reconcileAttempts(): void
    {
        if (! Schema::hasTable('assessment_attempts')) {
            return;
        }

        $columns = [
            'assessment_id' => fn (Blueprint $table) => $table->unsignedBigInteger('assessment_id')->nullable(),
            'learner_id' => fn (Blueprint $table) => $table->unsignedBigInteger('learner_id')->nullable(),
            'attempt_number' => fn (Blueprint $table) => $table->unsignedInteger('attempt_number')->default(1),
            'status' => fn (Blueprint $table) => $table->string('status', 24)->default('in_progress'),
            'earned_marks' => fn (Blueprint $table) => $table->decimal('earned_marks', 8, 2)->nullable(),
            'total_marks_snapshot' => fn (Blueprint $table) => $table->decimal('total_marks_snapshot', 8, 2)->nullable(),
            'passing_marks_snapshot' => fn (Blueprint $table) => $table->decimal('passing_marks_snapshot', 8, 2)->nullable(),
            'percentage' => fn (Blueprint $table) => $table->decimal('percentage', 6, 2)->nullable(),
            'passed' => fn (Blueprint $table) => $table->boolean('passed')->nullable(),
            'started_at' => fn (Blueprint $table) => $table->timestamp('started_at')->nullable(),
            'submitted_at' => fn (Blueprint $table) => $table->timestamp('submitted_at')->nullable(),
            'reviewed_at' => fn (Blueprint $table) => $table->timestamp('reviewed_at')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('assessment_attempts', $column)) {
                Schema::table('assessment_attempts', function (Blueprint $table) use ($definition): void {
                    $definition($table);
                });
            }
        }

        // Preserve legacy learner ownership when an older column name exists.
        foreach (['user_id', 'student_id'] as $legacyLearnerColumn) {
            if (Schema::hasColumn('assessment_attempts', $legacyLearnerColumn)
                && Schema::hasColumn('assessment_attempts', 'learner_id')) {
                DB::table('assessment_attempts')
                    ->whereNull('learner_id')
                    ->whereNotNull($legacyLearnerColumn)
                    ->update(['learner_id' => DB::raw($legacyLearnerColumn)]);

                break;
            }
        }

        // Preserve common legacy score fields where possible without deleting old columns.
        if (Schema::hasColumn('assessment_attempts', 'score')
            && Schema::hasColumn('assessment_attempts', 'earned_marks')) {
            DB::table('assessment_attempts')
                ->whereNull('earned_marks')
                ->whereNotNull('score')
                ->update(['earned_marks' => DB::raw('score')]);
        }
    }

    private function reconcileAnswers(): void
    {
        if (! Schema::hasTable('assessment_answers')) {
            return;
        }

        $columns = [
            'attempt_id' => fn (Blueprint $table) => $table->unsignedBigInteger('attempt_id')->nullable(),
            'question_id' => fn (Blueprint $table) => $table->unsignedBigInteger('question_id')->nullable(),
            'response' => fn (Blueprint $table) => $table->json('response')->nullable(),
            'question_snapshot' => fn (Blueprint $table) => $table->json('question_snapshot')->nullable(),
            'awarded_marks' => fn (Blueprint $table) => $table->decimal('awarded_marks', 8, 2)->nullable(),
            'is_correct' => fn (Blueprint $table) => $table->boolean('is_correct')->nullable(),
            'requires_review' => fn (Blueprint $table) => $table->boolean('requires_review')->default(false),
            'feedback' => fn (Blueprint $table) => $table->text('feedback')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('assessment_answers', $column)) {
                Schema::table('assessment_answers', function (Blueprint $table) use ($definition): void {
                    $definition($table);
                });
            }
        }
    }

    public function down(): void
    {
        // Compatibility migration: intentionally non-destructive.
    }
};
