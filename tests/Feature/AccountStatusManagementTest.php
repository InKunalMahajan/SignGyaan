<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountStatusManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_suspend_and_reactivate_a_learner(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.status.update', $learner), [
                'status' => User::STATUS_SUSPENDED,
            ])
            ->assertRedirect(route('admin.users.edit', $learner));

        $learner->refresh();

        $this->assertSame(User::STATUS_SUSPENDED, $learner->status);
        $this->assertNotNull($learner->suspended_at);

        $this->actingAs($admin)
            ->patch(route('admin.users.status.update', $learner), [
                'status' => User::STATUS_ACTIVE,
            ])
            ->assertRedirect(route('admin.users.edit', $learner));

        $learner->refresh();

        $this->assertSame(User::STATUS_ACTIVE, $learner->status);
        $this->assertNull($learner->suspended_at);
    }

    public function test_disabled_user_cannot_sign_in(): void
    {
        $user = User::factory()->disabled()->create([
            'password' => 'password123',
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_suspended_authenticated_user_is_logged_out_from_protected_routes(): void
    {
        $user = User::factory()->suspended()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_cannot_suspend_their_own_signed_in_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->patch(route('admin.users.status.update', $admin), [
                'status' => User::STATUS_SUSPENDED,
            ])
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('status');

        $this->assertSame(User::STATUS_ACTIVE, $admin->fresh()->status);
    }

    public function test_regular_admin_cannot_change_super_administrator_status(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.status.update', $superAdmin), [
                'status' => User::STATUS_SUSPENDED,
            ])
            ->assertForbidden();

        $this->assertSame(User::STATUS_ACTIVE, $superAdmin->fresh()->status);
    }

    public function test_successful_login_records_last_login_time(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
            'last_login_at' => null,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertNotNull($user->fresh()->last_login_at);
    }
}
