<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArchitectureSeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_teaching_management(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.teaching.index'))
            ->assertOk()
            ->assertSee('Teaching Management')
            ->assertSee('Teacher → Class / Batch → Learners → Course Assignments');
    }

    public function test_admin_can_open_progress_and_assessment(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.progress.index'))
            ->assertOk()
            ->assertSee('Progress & Assessment')
            ->assertSee('Needs Support')
            ->assertSee('Mastered');
    }

    public function test_non_admin_cannot_open_architecture_pages(): void
    {
        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $this->actingAs($learner)
            ->get(route('admin.teaching.index'))
            ->assertForbidden();

        $this->actingAs($learner)
            ->get(route('admin.progress.index'))
            ->assertForbidden();
    }
}
