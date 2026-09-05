<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardAssessmentOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_assessment_overview_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Assessment Overview Dashboard')
            ->assertSee('Total attempts')
            ->assertSee('Submitted')
            ->assertSee('Pass rate')
            ->assertSee('Average score')
            ->assertSee('Assessment Health')
            ->assertSee('Performance Snapshot')
            ->assertSee('Recent Assessment Attempts')
            ->assertSee('No assessment attempts have been recorded yet.')
            ->assertSee('aria-label="Assessment pass rate"', false)
            ->assertSee('aria-label="Average assessment score"', false);
    }

    public function test_guest_cannot_access_assessment_overview_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_teacher_cannot_access_assessment_overview_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
