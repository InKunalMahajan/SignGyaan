<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningActivity;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningStreakActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_dashboard_shows_current_and_longest_learning_streak(): void
    {
        Carbon::setTestNow('2026-09-05 12:00:00');
        $user = User::factory()->create(['role' => 'learner']);

        foreach (['2026-09-03 10:00:00', '2026-09-04 10:00:00', '2026-09-05 10:00:00'] as $date) {
            LearningActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'lesson_completed',
                'title' => 'Lesson completed',
                'occurred_at' => $date,
            ]);
        }

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Your learning streak')
            ->assertSee('3 days')
            ->assertSee('Active today')
            ->assertSee('Recent activity');
    }

    public function test_saving_a_published_lesson_records_learning_activity(): void
    {
        Carbon::setTestNow('2026-09-05 12:00:00');
        $user = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create(['name' => 'Digital Skills', 'slug' => 'digital-skills', 'sort_order' => 1, 'is_published' => true]);
        $course = Course::create(['subject_id' => $subject->id, 'title' => 'Computer Basics', 'slug' => 'computer-basics', 'level' => 'Beginner', 'sort_order' => 1, 'is_published' => true]);
        $unit = Unit::create(['course_id' => $course->id, 'title' => 'Getting Started', 'slug' => 'getting-started', 'sort_order' => 1, 'is_published' => true]);
        $lesson = Lesson::create(['unit_id' => $unit->id, 'title' => 'Introduction', 'slug' => 'introduction', 'content' => 'Intro', 'sort_order' => 1, 'is_published' => true]);

        $this->actingAs($user)
            ->post(route('learning-progress.store'), [
                'subject_slug' => $subject->slug,
                'course_slug' => $course->slug,
                'lesson_id' => $lesson->id,
                'action' => 'complete',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('learning_activities', [
            'user_id' => $user->id,
            'activity_type' => 'lesson_completed',
            'course_slug' => $course->slug,
            'lesson_id' => $lesson->id,
            'title' => 'Lesson completed',
        ]);
    }
}
