<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardQuickManagementActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_quick_management_actions(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Quick Management Actions')
            ->assertSee('Create Learning Content')
            ->assertSee('Build Learning Support')
            ->assertSee('Manage Platform Activity')
            ->assertSee('Add Subject')
            ->assertSee('Add Course')
            ->assertSee('Add Unit')
            ->assertSee('Add Lesson')
            ->assertSee('Add Practice Resource')
            ->assertSee('Add Assessment')
            ->assertSee('Add Media')
            ->assertSee('Add Vocabulary')
            ->assertSee('Manage Users')
            ->assertSee('Manage Teachers')
            ->assertSee('Manage Learners')
            ->assertSee('Assessment Results')
            ->assertSee('Open Bulk User Management')
            ->assertSee(route('admin.subjects.create'), false)
            ->assertSee(route('admin.courses.create'), false)
            ->assertSee(route('admin.lessons.create'), false)
            ->assertSee(route('admin.assessments.create'), false)
            ->assertSee(route('admin.users.bulk.index'), false);
    }

    public function test_teacher_cannot_access_quick_management_actions_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
