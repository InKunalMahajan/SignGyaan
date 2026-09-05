<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardUserStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_user_statistics(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
            'last_login_at' => now(),
        ]);

        User::factory()->learner()->create([
            'email_verified_at' => now(),
            'last_login_at' => now()->subDays(2),
        ]);

        User::factory()->teacher()->suspended()->create([
            'email_verified_at' => null,
            'last_login_at' => null,
        ]);

        User::factory()->learner()->disabled()->create([
            'email_verified_at' => null,
            'last_login_at' => now()->subDays(45),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSee('User Statistics Dashboard')
            ->assertSee('Account Health')
            ->assertSee('Role Breakdown')
            ->assertSee('Recent Users')
            ->assertSee('Active accounts')
            ->assertSee('Verified email')
            ->assertSee('Recent logins')
            ->assertSee('Suspended')
            ->assertSee('Disabled')
            ->assertSee('Unverified email');
    }

    public function test_dashboard_user_statistics_are_safe_when_no_non_admin_users_exist(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('User Statistics Dashboard')
            ->assertSee('100% of all accounts');
    }
}
