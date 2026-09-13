<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityPhase16Test extends TestCase
{
    use RefreshDatabase;

    public function test_web_responses_include_baseline_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        User::factory()->create([
            'email' => 'security@example.test',
            'password' => 'CorrectPass123',
            'is_active' => true,
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from('/login')
                ->post('/login', [
                    'email' => 'security@example.test',
                    'password' => 'WrongPass123',
                ])
                ->assertRedirect('/login');
        }

        $this->post('/login', [
            'email' => 'security@example.test',
            'password' => 'WrongPass123',
        ])->assertStatus(429);
    }

    public function test_registration_requires_stronger_password(): void
    {
        $this->from('/register')
            ->post('/register', [
                'name' => 'Security Learner',
                'email' => 'security.learner@example.test',
                'role' => 'learner',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('password');
    }

    public function test_authenticated_response_is_not_cacheable(): void
    {
        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $this->actingAs($learner)
            ->get('/learner/classes')
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_session_security_defaults_are_documented_in_configuration(): void
    {
        $config = file_get_contents(config_path('session.php'));
        $environment = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString("'encrypt' => env('SESSION_ENCRYPT', true)", $config);
        $this->assertStringContainsString("'same_site' => env('SESSION_SAME_SITE', 'strict')", $config);
        $this->assertStringContainsString("env('APP_ENV') === 'production'", $config);
        $this->assertStringContainsString('SESSION_ENCRYPT=true', $environment);
        $this->assertStringContainsString('SESSION_HTTP_ONLY=true', $environment);
        $this->assertStringContainsString('SESSION_SAME_SITE=strict', $environment);
    }
}
