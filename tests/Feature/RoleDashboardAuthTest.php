<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_dashboard_is_public(): void
    {
        $this->get(route('dashboard.guest'))
            ->assertOk()
            ->assertSee('Guest Dashboard');
    }

    public function test_private_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard/teacher')
            ->assertRedirect(route('login'));
    }

    public function test_registration_creates_role_and_signs_user_in(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test Learner',
            'email' => 'learner@example.com',
            'role' => 'learner',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'learner@example.com',
            'role' => 'learner',
        ]);
    }

    public function test_public_registration_cannot_create_admin_account(): void
    {
        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Unsafe Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors('role');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
    }

    public function test_dashboard_redirects_authenticated_user_to_assigned_role(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertRedirect(route('dashboard.role', ['role' => 'teacher']));
    }

    public function test_user_can_open_assigned_dashboard(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);

        $this->actingAs($parent)
            ->get(route('dashboard.role', ['role' => 'parents']))
            ->assertOk()
            ->assertSee('Parents Dashboard');
    }

    public function test_user_cannot_open_another_role_dashboard(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get(route('dashboard.role', ['role' => 'admin']))
            ->assertForbidden();
    }

    public function test_logout_ends_session_and_returns_to_guest_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('dashboard.guest'));

        $this->assertGuest();
    }
}
