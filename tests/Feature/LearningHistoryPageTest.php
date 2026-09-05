<?php

namespace Tests\Feature;

use App\Models\LearningActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningHistoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_learning_history(): void
    {
        $this->get(route('learning-history'))
            ->assertRedirect(route('login'));
    }

    public function test_learner_can_review_learning_history_in_reverse_chronological_order(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        LearningActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'lesson_saved',
            'subject_slug' => 'digital-skills',
            'course_slug' => 'computer-basics',
            'lesson_id' => 11,
            'lesson_key' => 'lesson-11',
            'title' => 'Saved Introduction',
            'metadata' => [
                'course_title' => 'Computer Basics',
                'lesson_title' => 'Introduction',
            ],
            'occurred_at' => now()->subDay(),
        ]);

        LearningActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'lesson_completed',
            'subject_slug' => 'digital-skills',
            'course_slug' => 'computer-basics',
            'lesson_id' => 12,
            'lesson_key' => 'lesson-12',
            'title' => 'Completed Computer Parts',
            'metadata' => [
                'course_title' => 'Computer Basics',
                'lesson_title' => 'Computer Parts',
            ],
            'occurred_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('learning-history'))
            ->assertOk()
            ->assertSee('Learning History')
            ->assertSee('Total events')
            ->assertSee('Lesson activity')
            ->assertSeeInOrder([
                'Completed Computer Parts',
                'Saved Introduction',
            ]);
    }

    public function test_history_is_scoped_to_signed_in_learner(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $other = User::factory()->create(['role' => 'learner']);

        LearningActivity::create([
            'user_id' => $other->id,
            'activity_type' => 'lesson_completed',
            'title' => 'Other Learner Activity',
            'occurred_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('learning-history'))
            ->assertOk()
            ->assertDontSee('Other Learner Activity')
            ->assertSee('No learning history yet');
    }
}
