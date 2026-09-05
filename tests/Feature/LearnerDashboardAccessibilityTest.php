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

class LearnerDashboardAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_exposes_accessible_mobile_shortcuts_and_progress_semantics(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $lesson] = $this->publishedCourse();

        LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 1,
            'current_lesson_key' => 'lesson-'.$lesson->id,
            'completed_lessons' => [],
            'last_accessed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-learner-dashboard', false)
            ->assertSee('data-dashboard-quick-nav', false)
            ->assertSee('href="#dashboard-continue"', false)
            ->assertSee('href="#assessment-progress-heading"', false)
            ->assertSee('id="dashboard-continue"', false)
            ->assertSee('role="progressbar"', false)
            ->assertSee('aria-valuemin="0"', false)
            ->assertSee('aria-valuemax="100"', false)
            ->assertSee('Learning History')
            ->assertSee('My Courses');
    }

    public function test_public_layout_loads_dashboard_accessibility_enhancement_script(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('js/learner-dashboard-accessibility.js', false)
            ->assertSee('Skip to main content');
    }

    public function test_dashboard_accessibility_script_keeps_keyboard_hash_and_progress_support(): void
    {
        $script = file_get_contents(public_path('js/learner-dashboard-accessibility.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString('focus-visible:ring-4', $script);
        $this->assertStringContainsString('aria-valuetext', $script);
        $this->assertStringContainsString("['Home', 'End']", $script);
        $this->assertStringContainsString('hashchange', $script);
        $this->assertStringContainsString('focusSectionFromHash', $script);
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
            'short_description' => 'Learn computer basics.',
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

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'slug' => 'introduction',
            'content' => 'Introduction lesson.',
            'estimated_duration_minutes' => 10,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$subject, $course, $lesson];
    }
}
