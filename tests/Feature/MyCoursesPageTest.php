<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningProgress;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyCoursesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_my_courses(): void
    {
        $this->get(route('my-courses'))
            ->assertRedirect(route('login'));
    }

    public function test_learner_sees_in_progress_and_completed_courses_with_resume_links(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        [$subjectA, $courseA, $unitA, $lessonA1, $lessonA2] = $this->publishedCourse('digital-skills', 'computer-basics');
        [$subjectB, $courseB, , $lessonB1, $lessonB2] = $this->publishedCourse('office-skills', 'office-basics');

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subjectA->slug,
            'subject_name' => $subjectA->name,
            'course_slug' => $courseA->slug,
            'course_title' => $courseA->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$lessonA2->id,
            'completed_lessons' => ['lesson-'.$lessonA1->id],
            'last_accessed_at' => now(),
        ]);

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subjectB->slug,
            'subject_name' => $subjectB->name,
            'course_slug' => $courseB->slug,
            'course_title' => $courseB->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$lessonB2->id,
            'completed_lessons' => ['lesson-'.$lessonB1->id, 'lesson-'.$lessonB2->id],
            'last_accessed_at' => now()->subDay(),
            'completed_at' => now()->subDay(),
        ]);

        $resumeUrl = route('courses.show', [
            'subject' => $subjectA->slug,
            'course' => $courseA->slug,
            'lesson' => 'lesson-'.$lessonA2->id,
        ]);

        $this->actingAs($learner)
            ->get(route('my-courses'))
            ->assertOk()
            ->assertSee('My Courses')
            ->assertSee($courseA->title)
            ->assertSee($courseB->title)
            ->assertSee($unitA->title)
            ->assertSee($lessonA2->title)
            ->assertSee('In progress')
            ->assertSee('Completed')
            ->assertSee('1 of 2 lessons completed')
            ->assertSee('50% complete')
            ->assertSee($resumeUrl, false)
            ->assertSee('Review Course');
    }

    public function test_unpublished_course_progress_is_not_shown_in_my_courses(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create([
            'name' => 'Hidden Subject',
            'slug' => 'hidden-subject',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Draft Course',
            'slug' => 'draft-course',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 1,
            'current_lesson_key' => 'lesson-999',
            'completed_lessons' => [],
            'last_accessed_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('my-courses'))
            ->assertOk()
            ->assertDontSee('Draft Course')
            ->assertSee('No saved courses yet');
    }

    private function publishedCourse(string $subjectSlug, string $courseSlug): array
    {
        $subject = Subject::create([
            'name' => str($subjectSlug)->replace('-', ' ')->title()->toString(),
            'slug' => $subjectSlug,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => str($courseSlug)->replace('-', ' ')->title()->toString(),
            'slug' => $courseSlug,
            'level' => 'Beginner',
            'short_description' => 'Published learner course.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lessonOne = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'slug' => 'introduction',
            'content' => 'Introduction lesson.',
            'estimated_duration_minutes' => 8,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lessonTwo = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Next Lesson',
            'slug' => 'next-lesson',
            'content' => 'Continue learning here.',
            'estimated_duration_minutes' => 12,
            'sort_order' => 2,
            'is_published' => true,
        ]);

        return [$subject, $course, $unit, $lessonOne, $lessonTwo];
    }
}
