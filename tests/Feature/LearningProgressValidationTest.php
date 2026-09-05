<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningProgressValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_is_derived_from_published_database_content(): void
    {
        $user = User::factory()->create();
        [$subject, $course, $firstLesson, $secondLesson] = $this->publishedCourse();

        $response = $this->actingAs($user)->post(route('learning-progress.store'), [
            'subject_slug' => $subject->slug,
            'course_slug' => $course->slug,
            'lesson_id' => $firstLesson->id,
            'action' => 'complete',
            'subject_name' => 'Spoofed subject',
            'course_title' => 'Spoofed course',
            'total_lessons' => 999,
            'next_lesson_key' => 'unit-99-lesson-99',
        ]);

        $response->assertRedirect(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$secondLesson->id,
        ]));

        $this->assertDatabaseHas('learning_progress', [
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$secondLesson->id,
        ]);

        $progress = $user->learningProgress()->firstOrFail();
        $this->assertSame(['lesson-'.$firstLesson->id], $progress->completed_lessons);
    }

    public function test_unpublished_or_unrelated_lessons_cannot_be_saved(): void
    {
        $user = User::factory()->create();
        [$subject, $course] = $this->publishedCourse();

        $draftUnit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Draft Unit',
            'slug' => 'draft-unit',
            'sort_order' => 99,
            'is_published' => false,
        ]);

        $draftLesson = Lesson::create([
            'unit_id' => $draftUnit->id,
            'title' => 'Hidden Lesson',
            'slug' => 'hidden-lesson',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)
            ->from(route('courses.show', ['subject' => $subject->slug, 'course' => $course->slug]))
            ->post(route('learning-progress.store'), [
                'subject_slug' => $subject->slug,
                'course_slug' => $course->slug,
                'lesson_id' => $draftLesson->id,
                'action' => 'save',
            ]);

        $response->assertSessionHasErrors('lesson_id');
        $this->assertDatabaseCount('learning_progress', 0);
    }

    public function test_completing_all_published_lessons_marks_course_complete(): void
    {
        $user = User::factory()->create();
        [$subject, $course, $firstLesson, $secondLesson] = $this->publishedCourse();

        foreach ([$firstLesson, $secondLesson] as $lesson) {
            $this->actingAs($user)->post(route('learning-progress.store'), [
                'subject_slug' => $subject->slug,
                'course_slug' => $course->slug,
                'lesson_id' => $lesson->id,
                'action' => 'complete',
            ]);
        }

        $progress = $user->learningProgress()->firstOrFail();

        $this->assertSame(2, $progress->total_lessons);
        $this->assertCount(2, $progress->completed_lessons);
        $this->assertNotNull($progress->completed_at);
    }

    private function publishedCourse(): array
    {
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Computer Foundations',
            'slug' => 'computer-foundations',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $firstLesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'slug' => 'introduction',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $secondLesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Hardware Basics',
            'slug' => 'hardware-basics',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        return [$subject, $course, $firstLesson, $secondLesson];
    }
}
