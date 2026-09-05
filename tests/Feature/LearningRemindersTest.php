<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningProgress;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Services\LearningReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_learner_receives_one_weekly_learning_reminder(): void
    {
        $user = User::factory()->create();
        [$subject, $course, $lesson] = $this->publishedCourse();

        $progress = LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 1,
            'current_lesson_key' => 'lesson-'.$lesson->id,
            'completed_lessons' => [],
            'video_progress' => [],
            'last_accessed_at' => now()->subDays(4),
        ]);

        $service = app(LearningReminderService::class);

        $this->assertTrue($service->sendForProgress($progress));
        $this->assertFalse($service->sendForProgress($progress));

        $user->refresh();
        $this->assertCount(1, $user->notifications);
        $this->assertSame('learning_reminder', data_get($user->notifications->first()->data, 'meta.event'));
    }

    public function test_recent_or_completed_learning_does_not_receive_a_reminder(): void
    {
        $user = User::factory()->create();
        [$subject, $course, $lesson] = $this->publishedCourse();

        $recent = LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 1,
            'current_lesson_key' => 'lesson-'.$lesson->id,
            'completed_lessons' => [],
            'video_progress' => [],
            'last_accessed_at' => now()->subDay(),
        ]);

        $this->assertFalse(app(LearningReminderService::class)->sendForProgress($recent));
        $this->assertCount(0, $user->fresh()->notifications);
    }

    public function test_learning_notification_preference_is_respected(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'enabled' => true,
                'learning' => false,
                'assessment' => true,
                'milestone' => true,
                'general' => true,
            ],
        ]);
        [$subject, $course, $lesson] = $this->publishedCourse();

        $progress = LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 1,
            'current_lesson_key' => 'lesson-'.$lesson->id,
            'completed_lessons' => [],
            'video_progress' => [],
            'last_accessed_at' => now()->subDays(5),
        ]);

        $this->assertFalse(app(LearningReminderService::class)->sendForProgress($progress));
        $this->assertCount(0, $user->fresh()->notifications);
    }

    private function publishedCourse(): array
    {
        $subject = Subject::create([
            'name' => 'Computer Skills',
            'slug' => 'computer-skills',
            'description' => 'Learn computer skills.',
            'is_published' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'description' => 'Computer basics course.',
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'content' => 'Introduction lesson.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$subject, $course, $lesson];
    }
}
