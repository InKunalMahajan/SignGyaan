<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCurriculumReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_curriculum_review_workspace(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->path();

        $this->actingAs($admin)
            ->get(route('admin.curriculum.index'))
            ->assertOk()
            ->assertSee($course->title)
            ->assertSee($teacher->name);

        $this->actingAs($admin)
            ->get(route('admin.curriculum.courses.show', $course))
            ->assertOk()
            ->assertSee($lesson->title)
            ->assertSee('readiness');
    }

    public function test_non_admin_cannot_open_curriculum_review_workspace(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('admin.curriculum.index'))
            ->assertForbidden();
    }

    public function test_teacher_publishing_a_new_lesson_sends_it_to_pending_review(): void
    {
        [$teacher, $learner, $class, $course, $unit] = $this->path(false);

        $this->actingAs($teacher)->post(route('teacher.courses.units.lessons.store', [$course, $unit]), [
            'title' => 'New Pending Lesson',
            'summary' => 'Summary',
            'isl_video_url' => 'https://example.com/isl',
            'notes' => 'Notes',
            'estimated_minutes' => 10,
            'status' => 'published',
        ])->assertRedirect();

        $this->assertDatabaseHas('lessons', [
            'course_unit_id' => $unit->id,
            'title' => 'New Pending Lesson',
            'status' => 'published',
            'review_status' => 'pending',
        ]);
    }

    public function test_pending_lesson_is_hidden_from_learner_until_admin_approves_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->path();
        $lesson->update(['review_status' => 'pending']);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $lesson]))
            ->assertNotFound();

        $this->actingAs($admin)
            ->patch(route('admin.curriculum.lessons.review', $lesson), [
                'review_status' => 'approved',
                'review_notes' => 'Ready for learners.',
            ])
            ->assertRedirect();

        $lesson->refresh();
        $this->assertSame('approved', $lesson->review_status);
        $this->assertSame($admin->id, $lesson->reviewed_by);
        $this->assertNotNull($lesson->reviewed_at);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $lesson]))
            ->assertOk();
    }

    public function test_changes_requested_requires_review_notes_and_hides_lesson(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->path();

        $this->actingAs($admin)
            ->patch(route('admin.curriculum.lessons.review', $lesson), [
                'review_status' => 'changes_requested',
                'review_notes' => '',
            ])
            ->assertSessionHasErrors('review_notes');

        $this->actingAs($admin)
            ->patch(route('admin.curriculum.lessons.review', $lesson), [
                'review_status' => 'changes_requested',
                'review_notes' => 'Add an ISL explanation and clearer notes.',
            ])
            ->assertRedirect();

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $lesson]))
            ->assertNotFound();
    }

    public function test_teacher_edit_resets_review_to_pending(): void
    {
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->path();
        $lesson->update(['review_status' => 'approved']);

        $this->actingAs($teacher)->put(route('teacher.courses.units.lessons.update', [$course, $unit, $lesson]), [
            'title' => 'Updated Lesson',
            'summary' => 'Updated summary',
            'isl_video_url' => 'https://example.com/new-isl',
            'notes' => 'Updated notes',
            'estimated_minutes' => 12,
            'position' => 1,
            'status' => 'published',
        ])->assertRedirect();

        $lesson->refresh();
        $this->assertSame('pending', $lesson->review_status);
        $this->assertNull($lesson->reviewed_by);
        $this->assertNull($lesson->reviewed_at);
    }

    public function test_admin_can_activate_and_deactivate_subject_and_course(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$teacher, $learner, $class, $course] = $this->path();
        $subject = $course->subject;

        $this->actingAs($admin)->patch(route('admin.curriculum.subjects.status', $subject))->assertRedirect();
        $this->assertFalse($subject->fresh()->is_active);

        $this->actingAs($admin)->patch(route('admin.curriculum.courses.status', $course))->assertRedirect();
        $this->assertFalse($course->fresh()->is_active);
    }

    private function path(bool $withLesson = true): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);

        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Digital Skills',
            'code' => 'SG-REV001',
            'subject' => 'Information Technology',
            'level' => 'Beginner',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology-review',
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Basics',
            'slug' => 'digital-basics-review',
            'is_active' => true,
        ]);
        $class->courses()->attach($course->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Core Unit',
            'position' => 1,
            'is_active' => true,
        ]);

        $lesson = null;
        if ($withLesson) {
            $lesson = Lesson::create([
                'course_unit_id' => $unit->id,
                'title' => 'Introduction Lesson',
                'summary' => 'Introduction summary',
                'isl_video_url' => 'https://example.com/isl',
                'notes' => 'Lesson notes',
                'estimated_minutes' => 10,
                'position' => 1,
                'status' => 'published',
                'review_status' => 'approved',
                'published_at' => now(),
            ]);
        }

        return [$teacher, $learner, $class, $course, $unit, $lesson];
    }
}
