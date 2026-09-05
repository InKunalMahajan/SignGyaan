<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\LearningActivity;
use App\Models\LearningProgress;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Services\AchievementNotificationService;
use App\Services\LearnerActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementMilestoneNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_completed_lesson_creates_a_milestone_notification(): void
    {
        $user = User::factory()->create();

        LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => 'computer-skills',
            'subject_name' => 'Computer Skills',
            'course_slug' => 'computer-basics',
            'course_title' => 'Computer Basics',
            'total_lessons' => 4,
            'current_lesson_key' => 'lesson-2',
            'completed_lessons' => ['lesson-1'],
            'video_progress' => [],
            'last_accessed_at' => now(),
        ]);

        app(AchievementNotificationService::class)->lessonCompleted($user);

        $notification = $user->fresh()->notifications()->firstOrFail();

        $this->assertSame('milestone', data_get($notification->data, 'category'));
        $this->assertSame('First lesson completed', data_get($notification->data, 'title'));
        $this->assertSame('lesson_milestone', data_get($notification->data, 'meta.event'));
    }

    public function test_three_day_learning_streak_creates_one_streak_milestone(): void
    {
        $user = User::factory()->create();

        foreach ([2, 1] as $daysAgo) {
            LearningActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'lesson_saved',
                'title' => 'Learning place saved',
                'occurred_at' => now()->subDays($daysAgo),
            ]);
        }

        app(LearnerActivityService::class)->record($user, [
            'activity_type' => 'lesson_saved',
            'title' => 'Learning place saved',
        ]);

        $streakNotifications = $user->fresh()->notifications
            ->filter(fn ($notification) => data_get($notification->data, 'meta.event') === 'streak_milestone');

        $this->assertCount(1, $streakNotifications);
        $this->assertSame('3-day learning streak', data_get($streakNotifications->first()->data, 'title'));
    }

    public function test_passing_first_assessment_creates_assessment_and_perfect_score_milestones(): void
    {
        $user = User::factory()->create();
        $assessment = $this->assessment();

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'attempt_number' => 1,
            'status' => 'in-progress',
            'started_at' => now()->subMinute(),
        ]);

        $attempt->update([
            'status' => 'submitted',
            'score_points' => 10,
            'max_points' => 10,
            'percentage' => 100,
            'passed' => true,
            'submitted_at' => now(),
        ]);

        $events = $user->fresh()->notifications
            ->map(fn ($notification) => data_get($notification->data, 'meta.event'))
            ->filter()
            ->values();

        $this->assertTrue($events->contains('assessment_milestone'));
        $this->assertTrue($events->contains('perfect_assessment'));
    }

    public function test_milestone_preference_can_disable_achievement_notifications(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'enabled' => true,
                'learning' => true,
                'assessment' => true,
                'milestone' => false,
                'general' => true,
            ],
        ]);

        LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => 'computer-skills',
            'subject_name' => 'Computer Skills',
            'course_slug' => 'computer-basics',
            'course_title' => 'Computer Basics',
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-2',
            'completed_lessons' => ['lesson-1'],
            'video_progress' => [],
            'last_accessed_at' => now(),
        ]);

        app(AchievementNotificationService::class)->lessonCompleted($user);

        $this->assertCount(0, $user->fresh()->notifications);
    }

    private function assessment(): Assessment
    {
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'is_published' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Basics',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $practice = PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => 'Basics Quiz',
            'slug' => 'basics-quiz',
            'kind' => 'practice',
            'resource_type' => 'quiz',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return Assessment::create([
            'practice_resource_id' => $practice->id,
            'passing_percentage' => 70,
            'max_attempts' => 3,
            'show_feedback' => true,
            'is_published' => true,
        ]);
    }
}
