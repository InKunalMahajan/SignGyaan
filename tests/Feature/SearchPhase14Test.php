<?php

namespace Tests\Feature;

use App\Models\LearningClass;
use App\Models\ParentLearnerLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchPhase14Test extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_global_search(): void
    {
        $this->get('/search?q=database')->assertRedirect('/login');
    }

    public function test_learner_search_returns_only_enrolled_classes(): void
    {
        $learner = User::factory()->create(['role' => 'learner', 'is_active' => true]);
        $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);

        $enrolled = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC Database Skills',
            'code' => 'FYJC-DB',
            'is_active' => true,
        ]);

        LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Private Database Batch',
            'code' => 'PRIVATE-DB',
            'is_active' => true,
        ]);

        $enrolled->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $this->actingAs($learner)
            ->get('/search?q=Database')
            ->assertOk()
            ->assertSee('FYJC Database Skills')
            ->assertDontSee('Private Database Batch');
    }

    public function test_teacher_search_returns_only_owned_classes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);
        $otherTeacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);

        LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'HTML Visual Learning',
            'code' => 'HTML-A',
            'is_active' => true,
        ]);

        LearningClass::create([
            'teacher_id' => $otherTeacher->id,
            'name' => 'HTML Hidden Class',
            'code' => 'HTML-X',
            'is_active' => true,
        ]);

        $this->actingAs($teacher)
            ->get('/search?q=HTML')
            ->assertOk()
            ->assertSee('HTML Visual Learning')
            ->assertDontSee('HTML Hidden Class');
    }

    public function test_parent_search_exposes_only_approved_linked_learners(): void
    {
        $parent = User::factory()->create(['role' => 'parents', 'is_active' => true]);
        $approved = User::factory()->create(['role' => 'learner', 'is_active' => true, 'name' => 'Asha Search Learner']);
        $pending = User::factory()->create(['role' => 'learner', 'is_active' => true, 'name' => 'Asha Pending Learner']);

        ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $approved->id,
            'relationship' => 'parent',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $pending->id,
            'relationship' => 'parent',
            'status' => 'pending',
        ]);

        $this->actingAs($parent)
            ->get('/search?q=Asha')
            ->assertOk()
            ->assertSee('Asha Search Learner')
            ->assertDontSee('Asha Pending Learner');
    }

    public function test_admin_search_can_find_platform_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
            'name' => 'Searchable Student',
            'email' => 'search.student@example.test',
        ]);

        $this->actingAs($admin)
            ->get('/search?q=Searchable')
            ->assertOk()
            ->assertSee('Searchable Student')
            ->assertSee('search.student@example.test');
    }

    public function test_short_query_shows_guidance_without_running_search(): void
    {
        $learner = User::factory()->create(['role' => 'learner', 'is_active' => true]);

        $this->actingAs($learner)
            ->get('/search?q=a')
            ->assertOk()
            ->assertSee('Enter at least 2 characters to search.');
    }
}
