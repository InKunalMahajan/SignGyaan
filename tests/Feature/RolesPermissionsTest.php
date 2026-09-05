<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_permission_matrix_is_enforced(): void
    {
        $service = app(RolePermissionService::class);

        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();
        $learner = User::factory()->learner()->create();

        $this->assertTrue($service->allows($superAdmin, RolePermissionService::PERMISSION_MANAGE_ROLES));
        $this->assertTrue($service->allows($admin, RolePermissionService::PERMISSION_MANAGE_USERS));
        $this->assertTrue($service->allows($teacher, RolePermissionService::PERMISSION_MANAGE_CONTENT));
        $this->assertFalse($service->allows($teacher, RolePermissionService::PERMISSION_ACCESS_ADMIN));
        $this->assertFalse($service->allows($learner, RolePermissionService::PERMISSION_MANAGE_CONTENT));
    }

    public function test_suspended_user_has_no_permissions(): void
    {
        $teacher = User::factory()->teacher()->suspended()->create();

        $this->assertFalse(
            app(RolePermissionService::class)->allows($teacher, RolePermissionService::PERMISSION_MANAGE_CONTENT)
        );
    }

    public function test_admin_console_accepts_admin_and_super_admin_only(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($superAdmin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($teacher)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_regular_admin_cannot_manage_super_admin_role(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $superAdmin))
            ->assertForbidden();

        $learner = User::factory()->learner()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $learner), [
                'name' => $learner->name,
                'email' => $learner->email,
                'role' => User::ROLE_SUPER_ADMIN,
                'admin_note' => null,
            ])
            ->assertSessionHasErrors('role');
    }

    public function test_super_admin_can_assign_teacher_role(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $learner = User::factory()->learner()->create();

        $this->actingAs($superAdmin)
            ->put(route('admin.users.update', $learner), [
                'name' => $learner->name,
                'email' => $learner->email,
                'role' => User::ROLE_TEACHER,
                'admin_note' => null,
            ])
            ->assertRedirect(route('admin.users.edit', $learner));

        $this->assertDatabaseHas('users', [
            'id' => $learner->id,
            'role' => User::ROLE_TEACHER,
        ]);
    }
}
