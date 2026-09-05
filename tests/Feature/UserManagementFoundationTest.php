<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserManagementFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_management_foundation_fields(): void
    {
        $this->assertTrue(Schema::hasColumns('users', [
            'role',
            'status',
            'last_login_at',
            'suspended_at',
            'admin_note',
        ]));
    }

    public function test_new_users_default_to_learner_and_active_status(): void
    {
        $user = User::factory()->create();

        $this->assertSame(User::ROLE_LEARNER, $user->role);
        $this->assertSame(User::STATUS_ACTIVE, $user->status);
        $this->assertTrue($user->isLearner());
        $this->assertTrue($user->isActive());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_factory_supports_admin_suspended_and_disabled_states(): void
    {
        $admin = User::factory()->admin()->create();
        $suspended = User::factory()->suspended()->create();
        $disabled = User::factory()->disabled()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($suspended->isSuspended());
        $this->assertNotNull($suspended->suspended_at);
        $this->assertTrue($disabled->isDisabled());
    }

    public function test_user_management_service_exposes_current_roles_and_account_statuses(): void
    {
        $service = app(UserManagementService::class);

        $this->assertSame([
            User::ROLE_LEARNER => 'Learner',
            User::ROLE_ADMIN => 'Administrator',
        ], $service->roles());

        $this->assertSame([
            User::STATUS_ACTIVE => 'Active',
            User::STATUS_SUSPENDED => 'Suspended',
            User::STATUS_DISABLED => 'Disabled',
        ], $service->statuses());
    }

    public function test_role_and_status_query_scopes_are_available(): void
    {
        User::factory()->count(2)->create();
        User::factory()->admin()->create();
        User::factory()->suspended()->create();

        $this->assertSame(1, User::query()->withRole(User::ROLE_ADMIN)->count());
        $this->assertSame(1, User::query()->withStatus(User::STATUS_SUSPENDED)->count());
        $this->assertSame(3, User::query()->withStatus(User::STATUS_ACTIVE)->count());
    }
}
