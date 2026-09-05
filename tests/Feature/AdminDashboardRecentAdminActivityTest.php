<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardRecentAdminActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_recent_management_activity_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create(['name' => 'Recent Learner']);
        $course = Course::factory()->create(['title' => 'Recent Course']);
        $lesson = Lesson::factory()->create(['title' => 'Recent Lesson']);
        Assessment::factory()->create();

        $learner->update(['name' => 'Updated Recent Learner']);
        $course->update(['title' => 'Updated Recent Course']);
        $lesson->update(['title' => 'Updated Recent Lesson']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Recent Admin Activity Dashboard')
            ->assertSee('Recent Management Changes')
            ->assertSee('7-Day Breakdown')
            ->assertSee('Updated Recent Learner')
            ->assertSee('Updated Recent Course')
            ->assertSee('Updated Recent Lesson')
            ->assertSee('This dashboard summarizes record timestamps. It is not a security audit log');
    }

    public function test_teacher_cannot_access_recent_admin_activity_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
