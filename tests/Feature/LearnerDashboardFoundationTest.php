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

class LearnerDashboardFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_learner_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_learner_dashboard_shows_saved_published_course_progress(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

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
            'short_description' => 'Learn essential computer skills.',
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
            'title' => 'What is a Computer?',
            'slug' => 'what-is-a-computer',
            'content' => 'Introduction to computers.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lessonTwo = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Computer Parts',
            'slug' => 'computer-parts',
            'content' => 'Learn the main computer parts.',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$lessonTwo->id,
            'completed_lessons' => ['lesson-'.$lessonOne->id],
            'last_accessed_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Welcome,')
            ->assertSee('Computer Basics')
            ->assertSee('Continue Learning')
            ->assertSee('1 of 2 lessons completed')
            ->assertSee('50%');
    }

    public function test_dashboard_does_not_surface_progress_for_unpublished_course_content(): void
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
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Draft Course')
            ->assertSee('Start your first course');
    }
}
