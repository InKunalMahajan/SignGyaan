<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_open_admin_user_management(): void
    {
        $user = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_search_and_filter_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'name' => 'Asha Teacher',
            'email' => 'asha.teacher@example.com',
            'role' => 'teacher',
        ]);
        User::factory()->create([
            'name' => 'Ravi Learner',
            'role' => 'learner',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'search' => 'Asha',
            'role' => 'teacher',
            'status' => 'active',
        ]));

        $response
            ->assertOk()
            ->assertSee($teacher->name)
            ->assertDontSee('Ravi Learner');
    }

    public function test_admin_can_create_user_with_any_managed_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Parent',
            'email' => 'parent@example.com',
            'role' => 'parents',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => '1',
        ]);

        $user = User::where('email', 'parent@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.users.show', $user));
        $this->assertDatabaseHas('users', [
            'email' => 'parent@example.com',
            'role' => 'parents',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_change_another_users_role_and_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'teacher',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'teacher',
            'is_active' => false,
        ]);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('admin.users.status', $admin))
            ->assertSessionHasErrors('status');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_cannot_remove_own_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'teacher',
                'is_active' => '1',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertSessionHasErrors('role');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_inactive_user_is_signed_out_of_protected_pages(): void
    {
        $user = User::factory()->create([
            'role' => 'learner',
            'is_active' => false,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.role', 'learner'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
