<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assessments')) {
            return;
        }

        // Legacy SignGyaan databases may contain a much older assessments table.
        // Add every Phase 5 column independently and never assume column order.
        if (! Schema::hasColumn('assessments', 'course_id')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->unsignedBigInteger('course_id')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'created_by')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->unsignedBigInteger('created_by')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'title')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->string('title')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'instructions')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->text('instructions')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'status')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->string('status', 24)->default('draft');
            });
        }

        if (! Schema::hasColumn('assessments', 'total_marks')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->decimal('total_marks', 8, 2)->default(0);
            });
        }

        if (! Schema::hasColumn('assessments', 'passing_marks')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->decimal('passing_marks', 8, 2)->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'duration_minutes')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->unsignedSmallInteger('duration_minutes')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'published_at')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->timestamp('published_at')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'created_at')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasColumn('assessments', 'updated_at')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->timestamp('updated_at')->nullable();
            });
        }

        // Backfill ownership only when the legacy row already has a valid course.
        if (Schema::hasColumn('assessments', 'created_by')
            && Schema::hasColumn('assessments', 'course_id')
            && Schema::hasTable('courses')
            && Schema::hasColumn('courses', 'created_by')) {
            DB::table('assessments')
                ->whereNull('created_by')
                ->whereNotNull('course_id')
                ->orderBy('id')
                ->chunkById(200, function ($assessments): void {
                    foreach ($assessments as $assessment) {
                        $creatorId = DB::table('courses')
                            ->where('id', $assessment->course_id)
                            ->value('created_by');

                        if ($creatorId !== null) {
                            DB::table('assessments')
                                ->where('id', $assessment->id)
                                ->update(['created_by' => $creatorId]);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        // Compatibility migration: intentionally non-destructive on rollback.
    }
};
