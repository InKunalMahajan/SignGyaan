<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningActivity;
use App\Models\LearningProgress;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerDashboardFinalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_learner_can_move_through_the_personal_learning_hub(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $firstLesson, $secondLesson] = $this->publishedCourse();

        LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$secondLesson->id,
            'completed_lessons' => ['lesson-'.$firstLesson->id],
            'video_progress' => [
                'lesson-'.$secondLesson->id => [
                    'position_seconds' => 30,
                    'duration_seconds' => 100,
                    'watched_percent' => 30,
                ],
            ],
            'last_accessed_at' => now(),
        ]);

        LearningActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'lesson_completed',
            'subject_slug' => $subject->slug,
            'course_slug' => $course->slug,
            'lesson_id' => $firstLesson->id,
            'lesson_key' => 'lesson-'.$firstLesson->id,
            'title' => 'Lesson completed',
            'metadata' => [
                'course_title' => $course->title,
                'lesson_title' => $firstLesson->title,
            ],
            'occurred_at' => now(),
        ]);

        $dashboard = $this->actingAs($user)->get(route('dashboard'));
        $dashboard
            ->assertOk()
            ->assertSee('Continue learning')
            ->assertSee('Recommended next lessons')
            ->assertSee('Assessment progress')
            ->assertSee('Your learning streak')
            ->assertSee('My Courses')
            ->assertSee('Learning History')
            ->assertSee('View Performance')
            ->assertSee('data-learner-dashboard', false)
            ->assertSee('data-dashboard-quick-nav', false);

        $this->actingAs($user)
            ->get(route('my-courses'))
            ->assertOk()
            ->assertSee($course->title)
            ->assertSee('50%');

        $this->actingAs($user)
            ->get(route('my-courses.progress', [$subject->slug, $course->slug]))
            ->assertOk()
            ->assertSee($firstLesson->title)
            ->assertSee($secondLesson->title)
            ->assertSee('50% complete');

        $this->actingAs($user)
            ->get(route('assessment-performance'))
            ->assertOk()
            ->assertSee('Assessment Performance');

        $this->actingAs($user)
            ->get(route('learning-history'))
            ->assertOk()
            ->assertSee('Learning History')
            ->assertSee($firstLesson->title);
    }

    public function test_personal_learning_pages_require_authentication(): void
    {
        foreach ([
            route('dashboard'),
            route('my-learning'),
            route('my-courses'),
            route('assessment-performance'),
            route('learning-history'),
        ] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_dashboard_accessibility_and_mobile_support_remain_integrated(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Skip to main content')
            ->assertSee('js/learner-dashboard-accessibility.js', false)
            ->assertSee('aria-label="Dashboard shortcuts"', false)
            ->assertSee('role="progressbar"', false);

        $script = file_get_contents(public_path('js/learner-dashboard-accessibility.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString('focus-visible:ring-4', $script);
        $this->assertStringContainsString('aria-valuetext', $script);
        $this->assertStringContainsString('hashchange', $script);
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

        $firstLesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'slug' => 'introduction',
            'content' => 'Introduction lesson.',
            'estimated_duration_minutes' => 8,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $secondLesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Using a Computer',
            'slug' => 'using-a-computer',
            'content' => 'Using a computer lesson.',
            'estimated_duration_minutes' => 12,
            'sort_order' => 2,
            'is_published' => true,
        ]);

        return [$subject, $course, $firstLesson, $secondLesson];
    }
}
