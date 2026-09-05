<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardFiltersReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard_filters_and_report_shortcuts(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Filters & Reports')
            ->assertSee('Academic Learner Filter')
            ->assertSee('Maharashtra State Board')
            ->assertSee('CBSE')
            ->assertSee('NIOS')
            ->assertSee('Standard 12')
            ->assertSee('Learner Progress Report')
            ->assertSee('Assessment Results Report')
            ->assertSee('Teacher Assignment Report')
            ->assertSee('Academic Content Report')
            ->assertSee('Apply learner filters');
    }

    public function test_teacher_cannot_access_dashboard_filters_and_reports(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
