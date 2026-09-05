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

class CourseLessonProgressTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_learner_can_review_course_unit_and_lesson_progress(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create(['name' => 'Digital Skills', 'slug' => 'digital-skills', 'sort_order' => 1, 'is_published' => true]);
        $course = Course::create(['subject_id' => $subject->id, 'title' => 'Computer Basics', 'slug' => 'computer-basics', 'level' => 'Beginner', 'short_description' => 'Learn basics.', 'sort_order' => 1, 'is_published' => true]);
        $unit = Unit::create(['course_id' => $course->id, 'title' => 'Getting Started', 'slug' => 'getting-started', 'sort_order' => 1, 'is_published' => true]);
        $one = Lesson::create(['unit_id' => $unit->id, 'title' => 'Introduction', 'slug' => 'introduction', 'content' => 'Intro', 'estimated_duration_minutes' => 8, 'sort_order' => 1, 'is_published' => true]);
        $two = Lesson::create(['unit_id' => $unit->id, 'title' => 'Using a Computer', 'slug' => 'using-a-computer', 'content' => 'Lesson', 'estimated_duration_minutes' => 12, 'sort_order' => 2, 'is_published' => true]);

        LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$two->id,
            'completed_lessons' => ['lesson-'.$one->id],
            'video_progress' => ['lesson-'.$two->id => ['watched_percent' => 40]],
            'last_accessed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('my-courses.progress', [$subject->slug, $course->slug]))
            ->assertOk()
            ->assertSee('50% complete')
            ->assertSee('Getting Started')
            ->assertSee('Introduction')
            ->assertSee('Using a Computer')
            ->assertSee('✓ Completed')
            ->assertSee('Current lesson')
            ->assertSee('Video watched 40%');
    }

    public function test_draft_course_is_not_available_on_progress_page(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create(['name' => 'Hidden', 'slug' => 'hidden', 'sort_order' => 1, 'is_published' => true]);
        Course::create(['subject_id' => $subject->id, 'title' => 'Draft Course', 'slug' => 'draft-course', 'level' => 'Beginner', 'sort_order' => 1, 'is_published' => false]);

        $this->actingAs($user)
            ->get(route('my-courses.progress', ['hidden', 'draft-course']))
            ->assertNotFound();
    }
}
