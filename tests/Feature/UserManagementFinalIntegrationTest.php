<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementFinalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_user_management(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_active_administrator_can_access_user_management_and_accessibility_assets_are_loaded(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Users Dashboard')
            ->assertSee('Skip to admin content')
            ->assertSee('admin-user-management-accessibility.js');
    }

    public function test_teacher_cannot_access_admin_user_management(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_inactive_administrator_is_removed_from_authenticated_user_management_access(): void
    {
        $admin = User::factory()->admin()->suspended()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_regular_administrator_cannot_change_roles_in_bulk(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.bulk.action'), [
                'user_ids' => [$learner->id],
                'bulk_action' => 'role',
                'role' => User::ROLE_TEACHER,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $learner->id,
            'role' => User::ROLE_LEARNER,
        ]);
    }

    public function test_super_administrator_can_change_a_learner_role_in_bulk(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $learner = User::factory()->learner()->create();

        $this->actingAs($superAdmin)
            ->patch(route('admin.users.bulk.action'), [
                'user_ids' => [$learner->id],
                'bulk_action' => 'role',
                'role' => User::ROLE_TEACHER,
            ])
            ->assertRedirect(route('admin.users.bulk.index'));

        $this->assertDatabaseHas('users', [
            'id' => $learner->id,
            'role' => User::ROLE_TEACHER,
        ]);
    }

    public function test_bulk_management_page_has_accessible_labels_and_live_regions(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        User::factory()->learner()->create(['name' => 'Accessibility Learner']);

        $this->actingAs($superAdmin)
            ->get(route('admin.users.bulk.index'))
            ->assertOk()
            ->assertSee('Bulk User Management')
            ->assertSee('aria-label="Select Accessibility Learner"', false)
            ->assertSee('role="status"', false)
            ->assertSee('aria-live="polite"', false);
    }
}
