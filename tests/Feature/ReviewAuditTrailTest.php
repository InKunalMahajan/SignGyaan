<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Lesson;
use App\Models\ReviewAuditEvent;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewAuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_cycle_creates_append_only_audit_events(): void
    {
        [$teacher, $admin, $course, $unit] = $this->path(false);

        $this->actingAs($teacher)->post(route('teacher.courses.units.lessons.store', [$course, $unit]), [
            'title' => 'Audit Lesson',
            'summary' => 'Summary',
            'isl_video_url' => 'https://example.com/isl',
            'notes' => 'Notes',
            'estimated_minutes' => 10,
            'status' => 'published',
        ])->assertRedirect();

        $lesson = Lesson::where('title', 'Audit Lesson')->firstOrFail();
        $this->assertDatabaseHas('review_audit_events', [
            'lesson_id' => $lesson->id,
            'event_type' => 'review_submitted',
            'actor_id' => $teacher->id,
            'to_review_status' => 'pending',
        ]);

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review', $lesson), [
            'review_status' => 'changes_requested',
            'review_notes' => 'Add a clearer ISL explanation.',
        ])->assertRedirect();

        $this->actingAs($teacher)->patch(route('teacher.reviews.resubmit', $lesson), [
            'teacher_response' => 'Updated the ISL explanation and notes.',
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review', $lesson), [
            'review_status' => 'approved',
            'review_notes' => 'Ready for learners.',
        ])->assertRedirect();

        $this->assertSame(4, ReviewAuditEvent::where('lesson_id', $lesson->id)->count());
        $this->assertDatabaseHas('review_audit_events', [
            'lesson_id' => $lesson->id,
            'event_type' => 'changes_requested',
            'review_notes' => 'Add a clearer ISL explanation.',
        ]);
        $this->assertDatabaseHas('review_audit_events', [
            'lesson_id' => $lesson->id,
            'event_type' => 'review_resubmitted',
            'teacher_response' => 'Updated the ISL explanation and notes.',
        ]);
        $this->assertDatabaseHas('review_audit_events', [
            'lesson_id' => $lesson->id,
            'event_type' => 'review_approved',
            'review_notes' => 'Ready for learners.',
        ]);
    }

    public function test_earlier_feedback_is_preserved_after_later_review_decisions(): void
    {
        [$teacher, $admin, $course, $unit, $lesson] = $this->path();

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review', $lesson), [
            'review_status' => 'changes_requested',
            'review_notes' => 'First feedback remains permanent.',
        ])->assertRedirect();

        $this->actingAs($teacher)->patch(route('teacher.reviews.resubmit', $lesson), [
            'teacher_response' => 'First response remains permanent.',
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review', $lesson), [
            'review_status' => 'approved',
            'review_notes' => 'Final approval.',
        ])->assertRedirect();

        $this->assertDatabaseHas('review_audit_events', ['review_notes' => 'First feedback remains permanent.']);
        $this->assertDatabaseHas('review_audit_events', ['teacher_response' => 'First response remains permanent.']);
        $this->assertDatabaseHas('review_audit_events', ['review_notes' => 'Final approval.']);
    }

    public function test_audit_snapshot_survives_lesson_deletion(): void
    {
        [$teacher, $admin, $course, $unit, $lesson] = $this->path();

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review', $lesson), [
            'review_status' => 'approved',
            'review_notes' => 'Permanent record.',
        ])->assertRedirect();

        $event = ReviewAuditEvent::where('event_type', 'review_approved')->firstOrFail();
        $lesson->delete();

        $event->refresh();
        $this->assertNull($event->lesson_id);
        $this->assertSame('Introduction Lesson', $event->lesson_title);
        $this->assertSame('Digital Basics', $event->course_title);
        $this->assertSame('Permanent record.', $event->review_notes);
    }

    public function test_admin_can_search_global_moderation_logs_and_non_admin_is_blocked(): void
    {
        [$teacher, $admin, $course, $unit, $lesson] = $this->path();

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review', $lesson), [
            'review_status' => 'changes_requested',
            'review_notes' => 'Searchable audit feedback.',
        ])->assertRedirect();

        $this->actingAs($admin)
            ->get(route('admin.reviews.history', ['q' => 'Searchable audit feedback']))
            ->assertOk()
            ->assertSee('Searchable audit feedback')
            ->assertSee('Introduction Lesson');

        $this->actingAs($teacher)->get(route('admin.reviews.history'))->assertForbidden();
    }

    public function test_teacher_can_view_only_history_for_their_own_lesson(): void
    {
        [$teacher, $admin, $course, $unit, $lesson] = $this->path();
        [$otherTeacher, $otherAdmin, $otherCourse, $otherUnit, $otherLesson] = $this->path(true, 'Other');

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review', $lesson), [
            'review_status' => 'approved',
            'review_notes' => 'Owned history.',
        ])->assertRedirect();

        $this->actingAs($teacher)
            ->get(route('teacher.reviews.history', $lesson))
            ->assertOk()
            ->assertSee('Owned history');

        $this->actingAs($teacher)
            ->get(route('teacher.reviews.history', $otherLesson))
            ->assertForbidden();
    }

    public function test_return_to_pending_is_audited(): void
    {
        [$teacher, $admin, $course, $unit, $lesson] = $this->path();

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review', $lesson), [
            'review_status' => 'pending',
            'review_notes' => 'Needs another review pass.',
        ])->assertRedirect();

        $this->assertDatabaseHas('review_audit_events', [
            'lesson_id' => $lesson->id,
            'event_type' => 'review_returned_pending',
            'to_review_status' => 'pending',
            'review_notes' => 'Needs another review pass.',
        ]);
    }

    private function path(bool $withLesson = true, string $suffix = ''): array
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology'.$suffix,
            'slug' => 'information-technology-audit'.strtolower($suffix),
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Basics'.$suffix,
            'slug' => 'digital-basics-audit'.strtolower($suffix),
            'is_active' => true,
        ]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Core Unit'.$suffix,
            'position' => 1,
            'is_active' => true,
        ]);

        $lesson = null;
        if ($withLesson) {
            $lesson = Lesson::create([
                'course_unit_id' => $unit->id,
                'title' => 'Introduction Lesson'.$suffix,
                'summary' => 'Introduction summary',
                'isl_video_url' => 'https://example.com/isl',
                'notes' => 'Lesson notes',
                'estimated_minutes' => 10,
                'position' => 1,
                'status' => 'published',
                'review_status' => 'pending',
                'review_submitted_at' => now(),
                'published_at' => now(),
            ]);
        }

        return [$teacher, $admin, $course, $unit, $lesson];
    }
}
