<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_active_admin_can_view_dashboard_foundation(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->learner()->create();
        User::factory()->teacher()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('SignGyaan Admin Dashboard')
            ->assertSee('Platform Overview')
            ->assertSee('Management Workspaces')
            ->assertSee('Learners')
            ->assertSee('Teachers')
            ->assertSee('Courses')
            ->assertSee('Lessons')
            ->assertSee('Assessments')
            ->assertSee('Progress Snapshot');
    }

    public function test_teacher_cannot_access_admin_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
