<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_profile_management_page(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->create([
            'admin_note' => 'Needs onboarding follow-up.',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.edit', $learner));

        $response->assertOk()
            ->assertSee('Profile & account details')
            ->assertSee($learner->name)
            ->assertSee($learner->email)
            ->assertSee('Needs onboarding follow-up.')
            ->assertSee('Account status')
            ->assertSee('Last login');
    }

    public function test_admin_can_update_core_profile_and_internal_note(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $learner), [
                'name' => 'Updated Learner',
                'email' => 'UPDATED@example.com',
                'role' => User::ROLE_LEARNER,
                'admin_note' => 'Internal support note.',
            ]);

        $response->assertRedirect(route('admin.users.edit', $learner));

        $this->assertDatabaseHas('users', [
            'id' => $learner->id,
            'name' => 'Updated Learner',
            'email' => 'updated@example.com',
            'role' => User::ROLE_LEARNER,
            'admin_note' => 'Internal support note.',
        ]);
    }

    public function test_admin_cannot_remove_their_own_admin_role(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => User::ROLE_LEARNER,
                'admin_note' => null,
            ]);

        $response->assertRedirect(route('admin.users.edit', $admin));
        $response->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_profile_update_does_not_change_account_status(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->suspended()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $learner), [
                'name' => $learner->name,
                'email' => $learner->email,
                'role' => User::ROLE_LEARNER,
                'admin_note' => 'Reviewed while suspended.',
            ])
            ->assertRedirect(route('admin.users.edit', $learner));

        $this->assertDatabaseHas('users', [
            'id' => $learner->id,
            'status' => User::STATUS_SUSPENDED,
        ]);
    }
}
