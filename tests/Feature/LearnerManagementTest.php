<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_learner_management_list(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create([
            'name' => 'Learner One',
            'education_board' => 'cbse',
            'standard' => '10',
            'academic_year' => '2026-27',
        ]);
        User::factory()->teacher()->create(['name' => 'Teacher One']);

        $response = $this->actingAs($admin)->get(route('admin.learners.index'));

        $response->assertOk();
        $response->assertSee('Learner One');
        $response->assertDontSee('Teacher One');
        $response->assertSee('CBSE');
    }

    public function test_learner_list_can_be_filtered_by_board_and_standard(): void
    {
        $admin = User::factory()->admin()->create();
        $cbseLearner = User::factory()->learner()->create([
            'name' => 'CBSE Learner',
            'education_board' => 'cbse',
            'standard' => '10',
        ]);
        User::factory()->learner()->create([
            'name' => 'NIOS Learner',
            'education_board' => 'nios',
            'standard' => '12',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.learners.index', [
            'board' => 'cbse',
            'standard' => '10',
        ]));

        $response->assertOk();
        $response->assertSee($cbseLearner->name);
        $response->assertDontSee('NIOS Learner');
    }

    public function test_admin_can_view_a_learner_detail_page(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create([
            'name' => 'Detail Learner',
            'education_board' => 'maharashtra',
            'standard' => '11',
            'academic_year' => '2026-27',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.learners.show', $learner));

        $response->assertOk();
        $response->assertSee('Detail Learner');
        $response->assertSee('Course Progress');
        $response->assertSee('Recent Assessment Attempts');
        $response->assertSee('Recent Learning Activity');
    }

    public function test_non_learner_cannot_be_opened_as_learner(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($admin)
            ->get(route('admin.learners.show', $teacher))
            ->assertNotFound();
    }

    public function test_learner_cannot_access_admin_learner_management(): void
    {
        $learner = User::factory()->learner()->create();

        $this->actingAs($learner)
            ->get(route('admin.learners.index'))
            ->assertForbidden();
    }
}
