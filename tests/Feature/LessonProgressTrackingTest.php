<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_a_published_lesson_starts_progress_without_duplicates(): void
    {
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->makeLearningPath();

        $route = route('learner.classes.courses.lessons.show', [$class, $course, $lesson]);

        $this->actingAs($learner)->get($route)->assertOk();
        $this->actingAs($learner)->get($route)->assertOk();

        $this->assertDatabaseCount('lesson_progress', 1);

        $progress = LessonProgress::firstOrFail();
        $this->assertSame('in_progress', $progress->status);
        $this->assertNotNull($progress->started_at);
        $this->assertNotNull($progress->last_viewed_at);
        $this->assertNull($progress->completed_at);
    }

    public function test_learner_can_complete_and_reopen_a_lesson(): void
    {
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->makeLearningPath();

        $this->actingAs($learner)
            ->patch(route('learner.classes.courses.lessons.progress.update', [$class, $course, $lesson]), [
                'status' => 'completed',
            ])
            ->assertRedirect();

        $progress = LessonProgress::where('learner_id', $learner->id)->where('lesson_id', $lesson->id)->firstOrFail();
        $this->assertSame('completed', $progress->status);
        $this->assertNotNull($progress->completed_at);

        $this->actingAs($learner)
            ->patch(route('learner.classes.courses.lessons.progress.update', [$class, $course, $lesson]), [
                'status' => 'in_progress',
            ])
            ->assertRedirect();

        $progress->refresh();
        $this->assertSame('in_progress', $progress->status);
        $this->assertNull($progress->completed_at);
    }

    public function test_course_page_shows_completion_percentage_and_resume_lesson(): void
    {
        [$teacher, $learner, $class, $course, $unit, $firstLesson] = $this->makeLearningPath();
        $secondLesson = $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now()->subHour(),
            'completed_at' => now()->subHour(),
        ]);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $secondLesson->id,
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(10),
            'last_viewed_at' => now(),
        ]);

        $response = $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$class, $course]));

        $response->assertOk();
        $response->assertSee('50%');
        $response->assertSee('Resume Learning');
        $response->assertSee('Second Lesson');
        $response->assertSee('Completed');
        $response->assertSee('In progress');
    }

    public function test_learner_cannot_track_a_lesson_from_a_class_they_are_not_enrolled_in(): void
    {
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->makeLearningPath();
        $class->learners()->detach($learner->id);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $lesson]))
            ->assertForbidden();

        $this->assertDatabaseMissing('lesson_progress', [
            'learner_id' => $learner->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_draft_and_hidden_unit_lessons_cannot_be_tracked(): void
    {
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->makeLearningPath();

        $draft = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Draft Lesson',
            'position' => 2,
            'status' => 'draft',
        ]);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $draft]))
            ->assertNotFound();

        $unit->update(['is_active' => false]);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $lesson]))
            ->assertNotFound();
    }

    public function test_progress_is_reused_when_same_course_is_assigned_to_another_enrolled_class(): void
    {
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->makeLearningPath();
        $secondLesson = $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
            'started_at' => now(),
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $secondClass = $this->makeClass($teacher, 'Second Class');
        $secondClass->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $secondClass->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$secondClass, $course]))
            ->assertOk()
            ->assertSee('50%');
    }

    public function test_teacher_can_view_progress_for_an_owned_class(): void
    {
        [$teacher, $learner, $class, $course, $unit, $firstLesson] = $this->makeLearningPath();
        $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
            'started_at' => now(),
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher.classes.progress', $class));

        $response->assertOk();
        $response->assertSee($learner->name);
        $response->assertSee('50%');
        $response->assertSee('1 / 2 completed');
    }

    public function test_teacher_cannot_view_another_teachers_class_progress(): void
    {
        [$teacher, $learner, $class] = $this->makeLearningPath();
        $otherTeacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($otherTeacher)
            ->get(route('teacher.classes.progress', $class))
            ->assertForbidden();
    }

    public function test_non_learner_cannot_use_lesson_progress_routes(): void
    {
        [$teacher, $learner, $class, $course, $unit, $lesson] = $this->makeLearningPath();

        $this->actingAs($teacher)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $lesson]))
            ->assertForbidden();
    }

    private function makeLearningPath(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher, 'Digital Skills');
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology',
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Basics',
            'slug' => 'digital-basics',
            'level' => 'Beginner',
            'is_active' => true,
        ]);

        $class->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Computer Basics',
            'position' => 1,
            'is_active' => true,
        ]);

        $lesson = $this->makeLesson($unit, 'Introduction to Computers', 1);

        return [$teacher, $learner, $class, $course, $unit, $lesson];
    }

    private function makeClass(User $teacher, string $name): LearningClass
    {
        return LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => $name,
            'code' => 'SG-'.strtoupper(substr(md5($name.$teacher->id), 0, 6)),
            'subject' => 'Information Technology',
            'level' => 'Beginner',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
    }

    private function makeLesson(CourseUnit $unit, string $title, int $position): Lesson
    {
        return Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => $title,
            'summary' => $title.' summary',
            'notes' => $title.' notes',
            'estimated_minutes' => 10,
            'position' => $position,
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
