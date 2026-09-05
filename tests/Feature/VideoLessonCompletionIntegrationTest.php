<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LearningProgress;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoLessonCompletionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_progress_is_saved_without_completing_the_lesson(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $lesson] = $this->publishedLesson();

        $this->actingAs($user)
            ->postJson(route('learning-progress.video.store'), [
                'subject_slug' => $subject->slug,
                'course_slug' => $course->slug,
                'lesson_id' => $lesson->id,
                'position_seconds' => 75,
                'duration_seconds' => 100,
            ])
            ->assertOk()
            ->assertJsonPath('watched_percent', 75)
            ->assertJsonPath('lesson_completed', false);

        $progress = LearningProgress::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertSame([], $progress->completed_lessons ?? []);
        $this->assertSame(75, data_get($progress->video_progress, 'lesson-'.$lesson->id.'.watched_percent'));
    }

    public function test_manual_complete_action_still_controls_lesson_completion(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $lesson] = $this->publishedLesson();

        $this->actingAs($user)->postJson(route('learning-progress.video.store'), [
            'subject_slug' => $subject->slug,
            'course_slug' => $course->slug,
            'lesson_id' => $lesson->id,
            'position_seconds' => 100,
            'duration_seconds' => 100,
        ])->assertOk();

        $this->actingAs($user)->post(route('learning-progress.store'), [
            'subject_slug' => $subject->slug,
            'course_slug' => $course->slug,
            'lesson_id' => $lesson->id,
            'action' => 'complete',
        ])->assertRedirect();

        $progress = LearningProgress::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertContains('lesson-'.$lesson->id, $progress->completed_lessons);
    }

    public function test_video_progress_rejects_a_lesson_outside_the_published_course(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$subject, $course] = $this->publishedLesson();

        $this->actingAs($user)
            ->postJson(route('learning-progress.video.store'), [
                'subject_slug' => $subject->slug,
                'course_slug' => $course->slug,
                'lesson_id' => 999999,
                'position_seconds' => 10,
                'duration_seconds' => 100,
            ])
            ->assertNotFound();
    }

    private function publishedLesson(): array
    {
        $subject = Subject::create(['name' => 'Digital Skills', 'slug' => 'digital-skills', 'sort_order' => 1, 'is_published' => true]);
        $course = Course::create(['subject_id' => $subject->id, 'title' => 'Computer Basics', 'slug' => 'computer-basics', 'sort_order' => 1, 'is_published' => true]);
        $unit = Unit::create(['course_id' => $course->id, 'title' => 'Getting Started', 'slug' => 'getting-started', 'sort_order' => 1, 'is_published' => true]);
        $lesson = Lesson::create(['unit_id' => $unit->id, 'title' => 'Input Devices', 'slug' => 'input-devices', 'isl_video_url' => 'https://example.com/input-devices.mp4', 'sort_order' => 1, 'is_published' => true]);

        return [$subject, $course, $lesson];
    }
}
