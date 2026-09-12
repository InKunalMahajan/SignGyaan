<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerDashboardLiveDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_learner_dashboard_uses_live_database_metrics_instead_of_demo_quiz_values(): void
    {
        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($learner)
            ->get(route('dashboard.role', 'learner'));

        $response
            ->assertOk()
            ->assertSee('Enrolled courses')
            ->assertSee('Lessons completed')
            ->assertSee('Lessons in progress')
            ->assertSee('No learning activity yet')
            ->assertDontSee('Average quiz score')
            ->assertDontSee('82%');
    }
}
