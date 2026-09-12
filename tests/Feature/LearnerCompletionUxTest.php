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

class LearnerCompletionUxTest extends TestCase
{
    use RefreshDatabase;

    public function test_completing_a_lesson_redirects_to_the_next_incomplete_lesson(): void
    {
        [$learner, $class, $course, $unit, $firstLesson] = $this->makeLearningPath();
        $secondLesson = $this->makeLesson($unit, 'Second Lesson', 2);

        $this->actingAs($learner)
            ->patch(route('learner.classes.courses.lessons.progress.update', [$class, $course, $firstLesson]), [
                'status' => 'completed',
            ])
            ->assertRedirect(route('learner.classes.courses.lessons.show', [$class, $course, $secondLesson]))
            ->assertSessionHas('status', 'Lesson complete. Continue with the next lesson.');

        $this->assertDatabaseHas('lesson_progress', [
            'learner_id' => $learner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_completing_the_final_remaining_lesson_redirects_to_course_completed_state(): void
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
            ->patch(route('learner.classes.courses.lessons.progress.update', [$class, $course, $secondLesson]), [
                'status' => 'completed',
            ])
            ->assertRedirect(route('learner.classes.courses.show', [$class, $course]))
            ->assertSessionHas('status', 'Course completed! You finished all published lessons.');

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$class, $course]))
            ->assertOk()
            ->assertSee('Course Completed')
            ->assertSee('You finished all published lessons.')
            ->assertSee('100%');
    }

    public function test_course_page_shows_chapter_completion_states_and_ignores_draft_lessons(): void
    {
        [$learner, $class, $course, $firstUnit, $firstLesson] = $this->makeLearningPath();
        $secondUnit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Second Chapter',
            'position' => 2,
            'is_active' => true,
        ]);
        $secondLesson = $this->makeLesson($secondUnit, 'Second Chapter Lesson', 1);

        Lesson::create([
            'course_unit_id' => $secondUnit->id,
            'title' => 'Draft Lesson',
            'position' => 2,
            'status' => 'draft',
        ]);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now()->subHour(),
            'completed_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$class, $course]));

        $response
            ->assertOk()
            ->assertSee('Chapter 1')
            ->assertSee('Chapter 2')
            ->assertSee('Completed')
            ->assertSee('Not started')
            ->assertSee('1/1 complete')
            ->assertDontSee('Draft Lesson')
            ->assertSee($secondLesson->title);
    }

    public function test_final_lesson_page_uses_complete_course_action(): void
    {
        [$learner, $class, $course, $unit, $lesson] = $this->makeLearningPath();

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $lesson]))
            ->assertOk()
            ->assertSee('Complete Course')
            ->assertSee('This is the final published lesson.');
    }

    private function makeLearningPath(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);

        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC IT',
            'code' => 'SG-FYJCIT',
            'subject' => 'Information Technology',
            'level' => '11',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
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
