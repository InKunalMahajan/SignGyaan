<?php

namespace Tests\Feature;

use App\Models\LearningActivity;
use App\Models\LearningProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardLearningActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_learning_activity_statistics(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create();

        LearningProgress::query()->create([
            'user_id' => $learner->id,
            'subject_slug' => 'information-technology',
            'subject_name' => 'Information Technology',
            'course_slug' => 'it-foundations',
            'course_title' => 'IT Foundations',
            'total_lessons' => 4,
            'current_lesson_key' => 'lesson-3',
            'completed_lessons' => ['lesson-1', 'lesson-2'],
            'last_accessed_at' => now(),
        ]);

        LearningActivity::query()->create([
            'user_id' => $learner->id,
            'activity_type' => 'lesson_completed',
            'subject_slug' => 'information-technology',
            'course_slug' => 'it-foundations',
            'lesson_key' => 'lesson-2',
            'title' => 'Completed Lesson 2',
            'occurred_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Learning Activity Dashboard')
            ->assertSee('Learners with progress')
            ->assertSee('Active learners')
            ->assertSee('Completed lessons')
            ->assertSee('Recently Active Learners')
            ->assertSee('Recent Learning Activity')
            ->assertSee('IT Foundations')
            ->assertSee('Completed Lesson 2');
    }

    public function test_learning_activity_dashboard_has_accessible_progress_information(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create();

        LearningProgress::query()->create([
            'user_id' => $learner->id,
            'subject_slug' => 'computer-basics',
            'subject_name' => 'Computer Basics',
            'course_slug' => 'basic-computer',
            'course_title' => 'Basic Computer',
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-2',
            'completed_lessons' => ['lesson-1'],
            'last_accessed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('aria-labelledby="learning-activity-heading"', false)
            ->assertSee('role="progressbar"', false)
            ->assertSee('aria-valuemin="0"', false)
            ->assertSee('aria-valuemax="100"', false)
            ->assertSee('aria-valuenow="50"', false);
    }

    public function test_teacher_cannot_access_learning_activity_admin_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
