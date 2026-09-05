<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningProgressNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_learning_place_creates_one_deduplicated_continue_notification(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $firstLesson] = $this->publishedCourse();

        $payload = [
            'subject_slug' => $subject->slug,
            'course_slug' => $course->slug,
            'lesson_id' => $firstLesson->id,
            'action' => 'save',
        ];

        $this->actingAs($user)->post(route('learning-progress.store'), $payload)->assertRedirect();
        $this->actingAs($user)->post(route('learning-progress.store'), $payload)->assertRedirect();

        $notifications = $user->fresh()->notifications;

        $this->assertCount(1, $notifications);
        $this->assertSame('learning', $notifications->first()->data['category']);
        $this->assertSame('Course saved for later', $notifications->first()->data['title']);
        $this->assertSame('learning_saved', $notifications->first()->data['meta']['event']);
        $this->assertSame('lesson-'.$firstLesson->id, $notifications->first()->data['meta']['lesson_key']);
    }

    public function test_completing_a_lesson_notifies_learner_about_next_published_lesson(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $firstLesson, $secondLesson] = $this->publishedCourse();

        $this->actingAs($user)
            ->post(route('learning-progress.store'), [
                'subject_slug' => $subject->slug,
                'course_slug' => $course->slug,
                'lesson_id' => $firstLesson->id,
                'action' => 'complete',
            ])
            ->assertRedirect(route('courses.show', [
                'subject' => $subject->slug,
                'course' => $course->slug,
                'lesson' => 'lesson-'.$secondLesson->id,
            ]));

        $notification = $user->fresh()->notifications()->firstOrFail();

        $this->assertSame('learning', $notification->data['category']);
        $this->assertSame('Next lesson ready', $notification->data['title']);
        $this->assertSame('lesson_completed', $notification->data['meta']['event']);
        $this->assertSame('lesson-'.$secondLesson->id, $notification->data['meta']['next_lesson_key']);
        $this->assertStringContainsString('lesson=lesson-'.$secondLesson->id, $notification->data['url']);
    }

    public function test_completing_final_lesson_creates_course_completion_milestone_notification(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $firstLesson, $secondLesson] = $this->publishedCourse();

        foreach ([$firstLesson, $secondLesson] as $lesson) {
            $this->actingAs($user)
                ->post(route('learning-progress.store'), [
                    'subject_slug' => $subject->slug,
                    'course_slug' => $course->slug,
                    'lesson_id' => $lesson->id,
                    'action' => 'complete',
                ])
                ->assertRedirect();
        }

        $courseCompletion = $user->fresh()->notifications
            ->first(fn ($notification) => ($notification->data['meta']['event'] ?? null) === 'course_completed');

        $this->assertNotNull($courseCompletion);
        $this->assertSame('milestone', $courseCompletion->data['category']);
        $this->assertSame('Course completed', $courseCompletion->data['title']);
        $this->assertSame(100, $courseCompletion->data['meta']['progress_percent']);
        $this->assertSame('Review Course', $courseCompletion->data['action_label']);
    }

    private function publishedCourse(): array
    {
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'short_description' => 'Learn computer basics.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $firstLesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'slug' => 'introduction',
            'content' => 'Introduction lesson.',
            'estimated_duration_minutes' => 8,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $secondLesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Using a Computer',
            'slug' => 'using-a-computer',
            'content' => 'Using a computer lesson.',
            'estimated_duration_minutes' => 12,
            'sort_order' => 2,
            'is_published' => true,
        ]);

        return [$subject, $course, $firstLesson, $secondLesson];
    }
}
