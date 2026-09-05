<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_dashboard_with_account_metrics(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        User::factory()->create([
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_ACTIVE,
        ]);

        User::factory()->create([
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_SUSPENDED,
            'suspended_at' => now(),
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response
            ->assertOk()
            ->assertSee('Users Dashboard')
            ->assertSee('Total users')
            ->assertSee('Active accounts')
            ->assertSee('Email verified')
            ->assertSee('Learning activity')
            ->assertSee('Suspended');
    }

    public function test_admin_can_filter_users_by_role_status_and_verification(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $target = User::factory()->create([
            'name' => 'Suspended Learner',
            'email' => 'suspended@example.com',
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_SUSPENDED,
            'suspended_at' => now(),
            'email_verified_at' => null,
        ]);

        User::factory()->create([
            'name' => 'Active Learner',
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_SUSPENDED,
            'verification' => 'unverified',
        ]));

        $response
            ->assertOk()
            ->assertSee($target->name)
            ->assertDontSee('Active Learner');
    }

    public function test_learner_cannot_access_admin_users_dashboard(): void
    {
        $learner = User::factory()->create([
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($learner)->get(route('admin.users.index'));

        $response->assertForbidden();
    }
}
