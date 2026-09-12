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

class LearnerContinueLearningTest extends TestCase
{
    use RefreshDatabase;

    public function test_continue_learning_prefers_latest_in_progress_accessible_lesson(): void
    {
        [$learner, $class, $course, $unit, $firstLesson] = $this->makeLearningPath();
        $secondLesson = $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'in_progress',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now()->subHour(),
        ]);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $secondLesson->id,
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(10),
            'last_viewed_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('learner.classes.index', ['continue' => 1]))
            ->assertRedirect(route('learner.classes.courses.lessons.show', [$class, $course, $secondLesson]));
    }

    public function test_continue_learning_uses_first_incomplete_lesson_when_nothing_is_in_progress(): void
    {
        [$learner, $class, $course, $unit, $firstLesson] = $this->makeLearningPath();
        $secondLesson = $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now()->subHour(),
            'completed_at' => now()->subHour(),
        ]);

        $this->actingAs($learner)
            ->get(route('learner.classes.index', ['continue' => 1]))
            ->assertRedirect(route('learner.classes.courses.lessons.show', [$class, $course, $secondLesson]));
    }

    public function test_continue_learning_falls_back_to_my_classes_when_no_lesson_is_available(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $this->actingAs($learner)
            ->get(route('learner.classes.index', ['continue' => 1]))
            ->assertRedirect(route('learner.classes.index'));
    }

    public function test_continue_learning_ignores_draft_and_inactive_curriculum(): void
    {
        [$learner, $class, $course, $unit, $publishedLesson] = $this->makeLearningPath();

        $draftLesson = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Draft Lesson',
            'position' => 0,
            'status' => 'draft',
        ]);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $draftLesson->id,
            'status' => 'in_progress',
            'started_at' => now()->subMinute(),
            'last_viewed_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('learner.classes.index', ['continue' => 1]))
            ->assertRedirect(route('learner.classes.courses.lessons.show', [$class, $course, $publishedLesson]));

        $unit->update(['is_active' => false]);

        $this->actingAs($learner)
            ->get(route('learner.classes.index', ['continue' => 1]))
            ->assertRedirect(route('learner.classes.index'));
    }

    public function test_learner_dashboard_counts_only_current_accessible_learning_content(): void
    {
        [$learner, $class, $course, $unit, $lesson] = $this->makeLearningPath();

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
            'started_at' => now()->subDay(),
            'last_viewed_at' => now()->subDay(),
            'completed_at' => now()->subDay(),
        ]);

        $hiddenUnit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Hidden Unit',
            'position' => 2,
            'is_active' => false,
        ]);
        $hiddenLesson = $this->makeLesson($hiddenUnit, 'Hidden Lesson', 1);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $hiddenLesson->id,
            'status' => 'in_progress',
            'started_at' => now(),
            'last_viewed_at' => now(),
        ]);

        $response = $this->actingAs($learner)->get(route('dashboard.role', 'learner'));

        $response
            ->assertOk()
            ->assertSee('Enrolled courses')
            ->assertSee('Lessons completed')
            ->assertSee('Lessons in progress')
            ->assertSee('1 completed this month')
            ->assertDontSee('Hidden Lesson');
    }

    private function makeLearningPath(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher);
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

        return [$learner, $class, $course, $unit, $lesson];
    }

    private function makeClass(User $teacher): LearningClass
    {
        return LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC IT',
            'code' => 'SG-FYJCIT',
            'subject' => 'Information Technology',
            'level' => '11',
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
