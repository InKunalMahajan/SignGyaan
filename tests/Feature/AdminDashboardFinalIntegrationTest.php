<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardFinalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_admin_can_access_complete_management_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('SignGyaan Admin Dashboard')
            ->assertSee('User Statistics Dashboard')
            ->assertSee('Academic Statistics Dashboard')
            ->assertSee('Learning Activity Dashboard')
            ->assertSee('Assessment Overview Dashboard')
            ->assertSee('Teacher Overview Dashboard')
            ->assertSee('Quick Management Actions')
            ->assertSee('Recent Admin Activity Dashboard')
            ->assertSee('Dashboard Filters & Reports');
    }

    public function test_dashboard_accessibility_landmarks_and_enhancements_are_present(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Skip to admin content')
            ->assertSee('id="admin-main-content"', false)
            ->assertSee('tabindex="-1"', false)
            ->assertSee('aria-label="Admin sidebar"', false)
            ->assertSee('aria-label="Open admin navigation"', false)
            ->assertSee('for="dashboard-board"', false)
            ->assertSee('for="dashboard-standard"', false)
            ->assertSee('js/admin-dashboard-accessibility.js');
    }

    public function test_guest_and_non_admin_roles_cannot_access_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $teacher = User::factory()->teacher()->create();
        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $learner = User::factory()->learner()->create();
        $this->actingAs($learner)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_inactive_admin_is_logged_out_and_redirected_to_login(): void
    {
        $admin = User::factory()->admin()->suspended()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_dashboard_and_authoring_routes_keep_security_middleware(): void
    {
        $dashboardRoute = app('router')->getRoutes()->getByName('admin.dashboard');
        $dashboardMiddleware = $dashboardRoute->gatherMiddleware();

        $this->assertContains('auth', $dashboardMiddleware);
        $this->assertContains('active', $dashboardMiddleware);
        $this->assertContains('admin', $dashboardMiddleware);

        foreach ([
            'admin.courses.builder.content-blocks.store',
            'admin.courses.builder.content-blocks.update',
            'admin.courses.builder.content-blocks.destroy',
        ] as $routeName) {
            $route = app('router')->getRoutes()->getByName($routeName);
            $middleware = $route->gatherMiddleware();

            $this->assertContains('auth', $middleware);
            $this->assertContains('active', $middleware);
            $this->assertContains('admin', $middleware);
            $this->assertContains('throttle:60,1', $middleware);
        }
    }
}
