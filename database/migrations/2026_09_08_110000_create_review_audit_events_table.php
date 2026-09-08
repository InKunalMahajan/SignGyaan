<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_audit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type', 50)->index();
            $table->string('from_review_status', 30)->nullable();
            $table->string('to_review_status', 30)->nullable();
            $table->string('actor_name', 160);
            $table->string('actor_role', 30);
            $table->string('lesson_title', 180);
            $table->string('course_title', 160)->nullable();
            $table->string('unit_title', 160)->nullable();
            $table->text('review_notes')->nullable();
            $table->text('teacher_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamp('created_at')->useCurrent();
        });

        // Preserve a starting snapshot for review records that existed before Step 18.
        $rows = DB::table('lessons')
            ->join('course_units', 'course_units.id', '=', 'lessons.course_unit_id')
            ->join('courses', 'courses.id', '=', 'course_units.course_id')
            ->leftJoin('users as reviewers', 'reviewers.id', '=', 'lessons.reviewed_by')
            ->select([
                'lessons.id as lesson_id',
                'lessons.title as lesson_title',
                'lessons.review_status',
                'lessons.review_notes',
                'lessons.teacher_response',
                'lessons.reviewed_by',
                'lessons.reviewed_at',
                'lessons.review_submitted_at',
                'course_units.title as unit_title',
                'courses.title as course_title',
                'reviewers.name as reviewer_name',
                'reviewers.role as reviewer_role',
            ])
            ->where('lessons.status', 'published')
            ->get();

        foreach ($rows as $row) {
            DB::table('review_audit_events')->insert([
                'lesson_id' => $row->lesson_id,
                'actor_id' => $row->reviewed_by,
                'event_type' => 'baseline_snapshot',
                'from_review_status' => null,
                'to_review_status' => $row->review_status,
                'actor_name' => $row->reviewer_name ?: 'System migration',
                'actor_role' => $row->reviewer_role ?: 'system',
                'lesson_title' => $row->lesson_title,
                'course_title' => $row->course_title,
                'unit_title' => $row->unit_title,
                'review_notes' => $row->review_notes,
                'teacher_response' => $row->teacher_response,
                'metadata' => json_encode(['source' => 'step_18_baseline']),
                'occurred_at' => $row->reviewed_at ?: ($row->review_submitted_at ?: now()),
                'created_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('review_audit_events');
    }
};
