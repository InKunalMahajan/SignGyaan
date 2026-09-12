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

        Schema::table('assessments', function (Blueprint $table) {
            if (! Schema::hasColumn('assessments', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('course_id');
            }

            if (! Schema::hasColumn('assessments', 'instructions')) {
                $table->text('instructions')->nullable();
            }

            if (! Schema::hasColumn('assessments', 'status')) {
                $table->string('status', 24)->default('draft');
            }

            if (! Schema::hasColumn('assessments', 'total_marks')) {
                $table->decimal('total_marks', 8, 2)->default(0);
            }

            if (! Schema::hasColumn('assessments', 'passing_marks')) {
                $table->decimal('passing_marks', 8, 2)->nullable();
            }

            if (! Schema::hasColumn('assessments', 'duration_minutes')) {
                $table->unsignedSmallInteger('duration_minutes')->nullable();
            }

            if (! Schema::hasColumn('assessments', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
        });

        if (Schema::hasColumn('assessments', 'created_by')
            && Schema::hasColumn('assessments', 'course_id')
            && Schema::hasTable('courses')
            && Schema::hasColumn('courses', 'created_by')) {
            DB::table('assessments')
                ->whereNull('created_by')
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
