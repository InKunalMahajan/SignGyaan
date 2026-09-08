<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TeacherReviewFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_review_queue_shows_only_owned_published_lessons_and_feedback(): void
    {
        [$teacher, $course, $unit] = $this->curriculum('Teacher One');
        $admin = User::factory()->create(['role' => 'admin']);
        $ownLesson = $this->lesson($unit, 'Needs Fixes', 'changes_requested', $admin, 'Add clearer ISL explanation.');

        $draft = $this->lesson($unit, 'Draft Lesson', 'pending');
        $draft->update(['status' => 'draft', 'published_at' => null]);

        [$otherTeacher, $otherCourse, $otherUnit] = $this->curriculum('Teacher Two');
        $otherLesson = $this->lesson($otherUnit, 'Other Teacher Lesson', 'changes_requested', $admin, 'Other feedback');

        $response = $this->actingAs($teacher)->get(route('teacher.reviews.index'));

        $response->assertOk();
        $response->assertSee($ownLesson->title);
        $response->assertSee('Add clearer ISL explanation.');
        $response->assertDontSee($draft->title);
        $response->assertDontSee($otherLesson->title);
        $response->assertViewHas('changesRequestedCount', 1);
    }

    public function test_editing_changes_requested_lesson_keeps_feedback_open_until_resubmission(): void
    {
        [$teacher, $course, $unit] = $this->curriculum();
        $admin = User::factory()->create(['role' => 'admin']);
        $lesson = $this->lesson($unit, 'Needs Fixes', 'changes_requested', $admin, 'Add more notes.');

        $this->actingAs($teacher)
            ->put(route('teacher.courses.units.lessons.update', [$course, $unit, $lesson]), [
                'title' => 'Needs Fixes Updated',
                'summary' => 'Improved summary',
                'isl_video_url' => 'https://example.com/new-isl',
                'notes' => 'Improved notes',
                'estimated_minutes' => 15,
                'position' => 1,
                'status' => 'published',
            ])
            ->assertRedirect();

        $lesson->refresh();
        $this->assertSame('changes_requested', $lesson->review_status);
        $this->assertSame('Add more notes.', $lesson->review_notes);
        $this->assertSame($admin->id, $lesson->reviewed_by);
        $this->assertNull($lesson->teacher_response);
    }

    public function test_teacher_can_resubmit_changes_requested_lesson_with_response(): void
    {
        [$teacher, $course, $unit] = $this->curriculum();
        $admin = User::factory()->create(['role' => 'admin']);
        $lesson = $this->lesson($unit, 'Needs Fixes', 'changes_requested', $admin, 'Add an ISL video.');

        $this->actingAs($teacher)
            ->patch(route('teacher.reviews.resubmit', $lesson), [
                'teacher_response' => 'Added the ISL video and expanded the notes.',
            ])
            ->assertRedirect(route('teacher.reviews.index', ['review' => 'pending']));

        $lesson->refresh();
        $this->assertSame('pending', $lesson->review_status);
        $this->assertSame('Added the ISL video and expanded the notes.', $lesson->teacher_response);
        $this->assertNotNull($lesson->review_submitted_at);
        $this->assertSame('Add an ISL video.', $lesson->review_notes);
        $this->assertSame($admin->id, $lesson->reviewed_by);
    }

    public function test_teacher_cannot_resubmit_approved_or_pending_lesson(): void
    {
        [$teacher, $course, $unit] = $this->curriculum();
        $lesson = $this->lesson($unit, 'Approved Lesson', 'approved');

        $this->actingAs($teacher)
            ->patch(route('teacher.reviews.resubmit', $lesson), ['teacher_response' => 'Trying again'])
            ->assertSessionHasErrors('teacher_response');

        $lesson->update(['review_status' => 'pending']);

        $this->actingAs($teacher)
            ->patch(route('teacher.reviews.resubmit', $lesson), ['teacher_response' => 'Trying again'])
            ->assertSessionHasErrors('teacher_response');
    }

    public function test_teacher_cannot_resubmit_another_teachers_lesson(): void
    {
        [$owner, $course, $unit] = $this->curriculum('Owner Teacher');
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $lesson = $this->lesson($unit, 'Owner Lesson', 'changes_requested');

        $this->actingAs($otherTeacher)
            ->patch(route('teacher.reviews.resubmit', $lesson), ['teacher_response' => 'Not mine'])
            ->assertForbidden();
    }

    public function test_non_teacher_cannot_open_teacher_review_queue(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get(route('teacher.reviews.index'))
            ->assertForbidden();
    }

    public function test_admin_can_see_teacher_response_after_resubmission(): void
    {
        [$teacher, $course, $unit] = $this->curriculum();
        $admin = User::factory()->create(['role' => 'admin']);
        $lesson = $this->lesson($unit, 'Needs Fixes', 'changes_requested', $admin, 'Add more detail.');

        $this->actingAs($teacher)
            ->patch(route('teacher.reviews.resubmit', $lesson), [
                'teacher_response' => 'Added examples and detailed notes.',
            ]);

        $this->actingAs($admin)
            ->get(route('admin.curriculum.courses.show', $course))
            ->assertOk()
            ->assertSee('Teacher response:')
            ->assertSee('Added examples and detailed notes.');
    }

    public function test_publishing_new_lesson_records_review_submission_time(): void
    {
        [$teacher, $course, $unit] = $this->curriculum();

        $this->actingAs($teacher)
            ->post(route('teacher.courses.units.lessons.store', [$course, $unit]), [
                'title' => 'New Review Lesson',
                'summary' => 'Summary',
                'isl_video_url' => 'https://example.com/isl',
                'notes' => 'Notes',
                'estimated_minutes' => 10,
                'status' => 'published',
            ])
            ->assertRedirect();

        $lesson = Lesson::where('title', 'New Review Lesson')->firstOrFail();
        $this->assertSame('pending', $lesson->review_status);
        $this->assertNotNull($lesson->review_submitted_at);
    }

    private function curriculum(string $teacherName = 'Teacher'): array
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'name' => $teacherName]);
        $suffix = Str::lower(Str::random(8));

        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology '.$suffix,
            'slug' => 'information-technology-'.$suffix,
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Basics '.$suffix,
            'slug' => 'digital-basics-'.$suffix,
            'is_active' => true,
        ]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Core Unit',
            'position' => 1,
            'is_active' => true,
        ]);

        return [$teacher, $course, $unit];
    }

    private function lesson(
        CourseUnit $unit,
        string $title,
        string $reviewStatus,
        ?User $reviewer = null,
        ?string $reviewNotes = null
    ): Lesson {
        return Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => $title,
            'summary' => 'Summary',
            'isl_video_url' => 'https://example.com/isl',
            'notes' => 'Notes',
            'estimated_minutes' => 10,
            'position' => 1,
            'status' => 'published',
            'review_status' => $reviewStatus,
            'reviewed_by' => $reviewer?->id,
            'reviewed_at' => $reviewer ? now() : null,
            'review_notes' => $reviewNotes,
            'review_submitted_at' => now()->subHour(),
            'published_at' => now(),
        ]);
    }
}
