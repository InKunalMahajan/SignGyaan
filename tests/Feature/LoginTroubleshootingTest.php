<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTroubleshootingTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_registered_user_can_log_in_with_correct_password(): void
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'SecurePass123',
            'is_active' => true,
            'role' => 'learner',
        ]);

        $this->post(route('login.store'), [
            'email' => 'student@example.com',
            'password' => 'SecurePass123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_trims_email_whitespace_and_normalizes_case(): void
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'SecurePass123',
            'is_active' => true,
        ]);

        $this->post(route('login.store'), [
            'email' => '  STUDENT@EXAMPLE.COM  ',
            'password' => 'SecurePass123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'SecurePass123',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => 'inactive@example.com',
            'password' => 'SecurePass123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_incorrect_password_cannot_log_in(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'SecurePass123',
            'is_active' => true,
        ]);

        $this->post(route('login.store'), [
            'email' => 'student@example.com',
            'password' => 'WrongPass123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
