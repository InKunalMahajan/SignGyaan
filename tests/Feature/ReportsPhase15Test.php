<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsPhase15Test extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_reports(): void
    {
        $this->get('/reports')->assertRedirect('/login');
    }

    public function test_learner_can_open_own_progress_report(): void
    {
        $learner = User::factory()->create(['role' => 'learner', 'is_active' => true]);

        $this->actingAs($learner)
            ->get('/reports')
            ->assertOk()
            ->assertSee('My Progress Report')
            ->assertSee('Export CSV');
    }

    public function test_teacher_can_open_teaching_report(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);

        $this->actingAs($teacher)
            ->get('/reports')
            ->assertOk()
            ->assertSee('Teaching Report');
    }

    public function test_parent_can_open_linked_learner_report(): void
    {
        $parent = User::factory()->create(['role' => 'parents', 'is_active' => true]);

        $this->actingAs($parent)
            ->get('/reports')
            ->assertOk()
            ->assertSee('Learner Progress Report');
    }

    public function test_admin_can_open_platform_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->get('/reports')
            ->assertOk()
            ->assertSee('Platform Report');
    }

    public function test_authenticated_user_can_export_csv(): void
    {
        $learner = User::factory()->create(['role' => 'learner', 'is_active' => true]);

        $response = $this->actingAs($learner)->get('/reports/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('content-disposition'));
    }

    public function test_reports_navigation_is_loaded_with_frontend_runtime(): void
    {
        $bootstrap = file_get_contents(resource_path('js/bootstrap.js'));
        $reports = file_get_contents(resource_path('js/reports.js'));

        $this->assertStringContainsString("import './reports';", $bootstrap);
        $this->assertStringContainsString("link.href = '/reports';", $reports);
        $this->assertStringContainsString("link.textContent = 'Reports';", $reports);
    }
}
