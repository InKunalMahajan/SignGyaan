<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TestingPhase17Test extends TestCase
{
    use RefreshDatabase;

    public function test_health_and_public_entry_points_are_available(): void
    {
        $this->get('/up')->assertOk();
        $this->get('/')->assertOk();
        $this->get('/dashboard/guest')->assertOk();
    }

    public function test_critical_application_routes_are_registered(): void
    {
        foreach ([
            'login',
            'register',
            'dashboard',
            'dashboard.role',
            'search.index',
            'reports.index',
            'reports.export',
            'learner.classes.index',
            'learner.assessments.index',
            'learner.progress.index',
            'teacher.classes.index',
            'teacher.assessments.index',
            'teacher.progress.index',
            'teacher.ai.index',
            'teacher.rag.index',
            'admin.users.index',
            'admin.curriculum.index',
        ] as $routeName) {
            $this->assertTrue(Route::has($routeName), "Expected route [{$routeName}] to be registered.");
        }
    }

    public function test_guests_are_redirected_from_private_cross_role_features(): void
    {
        $this->get('/search?q=HTML')->assertRedirect('/login');
        $this->get('/reports')->assertRedirect('/login');
        $this->get('/learner/classes')->assertRedirect('/login');
        $this->get('/teacher/classes')->assertRedirect('/login');
        $this->get('/admin/users')->assertRedirect('/login');
    }

    public function test_each_active_role_can_open_its_own_dashboard(): void
    {
        foreach (['learner', 'parents', 'teacher', 'admin'] as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'is_active' => true,
            ]);

            $this->actingAs($user)
                ->get("/dashboard/{$role}")
                ->assertOk();
        }
    }

    public function test_roles_cannot_open_another_roles_dashboard(): void
    {
        $matrix = [
            'learner' => 'teacher',
            'parents' => 'admin',
            'teacher' => 'learner',
            'admin' => 'parents',
        ];

        foreach ($matrix as $role => $otherRole) {
            $user = User::factory()->create([
                'role' => $role,
                'is_active' => true,
            ]);

            $this->actingAs($user)
                ->get("/dashboard/{$otherRole}")
                ->assertForbidden();
        }
    }

    public function test_inactive_account_cannot_use_protected_workspace(): void
    {
        $user = User::factory()->create([
            'role' => 'learner',
            'is_active' => false,
        ]);

        $this->actingAs($user)
            ->get('/learner/classes')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
