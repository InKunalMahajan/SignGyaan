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

class LearnerCourseProgressStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_card_moves_from_start_to_resume_to_review(): void
    {
        [$learner, $class, $course, $lesson] = $this->makeLearningPath();

        $this->actingAs($learner)
            ->get(route('learner.classes.show', $class))
            ->assertOk()
            ->assertSee('Not started')
            ->assertSee('0 of 1 lessons completed')
            ->assertSee('Start Learning');

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(5),
            'last_viewed_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('learner.classes.show', $class))
            ->assertOk()
            ->assertSee('In progress')
            ->assertSee('Resume Learning');

        LessonProgress::where('learner_id', $learner->id)
            ->where('lesson_id', $lesson->id)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'last_viewed_at' => now(),
            ]);

        $this->actingAs($learner)
            ->get(route('learner.classes.show', $class))
            ->assertOk()
            ->assertSee('Completed')
            ->assertSee('1 of 1 lessons completed')
            ->assertSee('100%')
            ->assertSee('Review Course');
    }

    public function test_course_card_counts_only_active_units_and_published_lessons(): void
    {
        [$learner, $class, $course, $lesson] = $this->makeLearningPath();

        $hiddenUnit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Hidden Chapter',
            'position' => 2,
            'is_active' => false,
        ]);

        Lesson::create([
            'course_unit_id' => $hiddenUnit->id,
            'title' => 'Hidden Lesson',
            'position' => 1,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $activeUnit = CourseUnit::where('course_id', $course->id)->where('is_active', true)->firstOrFail();

        Lesson::create([
            'course_unit_id' => $activeUnit->id,
            'title' => 'Draft Lesson',
            'position' => 2,
            'status' => 'draft',
        ]);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('learner.classes.show', $class))
            ->assertOk()
            ->assertSee('1 of 1 lessons completed')
            ->assertSee('100%')
            ->assertDontSee('Hidden Lesson')
            ->assertDontSee('Draft Lesson');
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

        $lesson = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Introduction to Computers',
            'summary' => 'Introduction summary',
            'notes' => 'Introduction notes',
            'estimated_minutes' => 10,
            'position' => 1,
            'status' => 'published',
            'published_at' => now(),
        ]);

        return [$learner, $class, $course, $lesson];
    }
}
