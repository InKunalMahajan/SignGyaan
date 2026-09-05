<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\User;
use Carbon\Carbon;

class AchievementNotificationService
{
    private const LESSON_MILESTONES = [1, 5, 10, 25, 50, 100];
    private const STREAK_MILESTONES = [3, 7, 14, 30, 60, 100];
    private const ASSESSMENT_PASS_MILESTONES = [1, 5, 10, 25, 50];

    public function __construct(private InAppNotificationService $notifications)
    {
    }

    public function lessonCompleted(User $user): void
    {
        $completedLessons = $user->learningProgress()
            ->get()
            ->sum(fn ($progress) => $progress->completedLessonsCount());

        if (! in_array($completedLessons, self::LESSON_MILESTONES, true)) {
            return;
        }

        $this->notifications->sendOnce(
            user: $user,
            dedupeKey: 'achievement:lessons:'.$completedLessons,
            category: 'milestone',
            title: $completedLessons === 1 ? 'First lesson completed' : $completedLessons.' lessons completed',
            message: $completedLessons === 1
                ? 'You completed your first SignGyaan lesson. Keep building your learning journey.'
                : 'You have completed '.$completedLessons.' lessons on SignGyaan. Great progress.',
            url: route('dashboard'),
            actionLabel: 'View Dashboard',
            meta: [
                'event' => 'lesson_milestone',
                'milestone' => $completedLessons,
            ],
        );
    }

    public function activityRecorded(User $user): void
    {
        $streak = $this->currentStreak($user);

        if (! in_array($streak, self::STREAK_MILESTONES, true)) {
            return;
        }

        $this->notifications->sendOnce(
            user: $user,
            dedupeKey: 'achievement:streak:'.$streak,
            category: 'milestone',
            title: $streak.'-day learning streak',
            message: 'You have been active on SignGyaan for '.$streak.' days in a row. Keep the streak going.',
            url: route('learning-history'),
            actionLabel: 'View Learning History',
            meta: [
                'event' => 'streak_milestone',
                'milestone' => $streak,
            ],
        );
    }

    public function assessmentSubmitted(AssessmentAttempt $attempt): void
    {
        if ($attempt->status !== 'submitted' || ! $attempt->passed) {
            return;
        }

        $attempt->loadMissing('user');
        $user = $attempt->user;

        if (! $user) {
            return;
        }

        $passedCount = $user->assessmentAttempts()
            ->where('status', 'submitted')
            ->where('passed', true)
            ->count();

        if (in_array($passedCount, self::ASSESSMENT_PASS_MILESTONES, true)) {
            $this->notifications->sendOnce(
                user: $user,
                dedupeKey: 'achievement:assessments-passed:'.$passedCount,
                category: 'milestone',
                title: $passedCount === 1 ? 'First assessment passed' : $passedCount.' assessments passed',
                message: $passedCount === 1
                    ? 'You passed your first SignGyaan assessment.'
                    : 'You have now passed '.$passedCount.' SignGyaan assessments.',
                url: route('assessment-performance'),
                actionLabel: 'View Performance',
                meta: [
                    'event' => 'assessment_milestone',
                    'milestone' => $passedCount,
                    'attempt_id' => $attempt->id,
                ],
            );
        }

        if ((float) $attempt->percentage >= 100) {
            $this->notifications->sendOnce(
                user: $user,
                dedupeKey: 'achievement:perfect-assessment:first',
                category: 'milestone',
                title: 'Perfect assessment score',
                message: 'You achieved a 100% assessment score on SignGyaan.',
                url: route('assessment-attempts.result', [$attempt->assessment_id, $attempt]),
                actionLabel: 'Review Result',
                meta: [
                    'event' => 'perfect_assessment',
                    'attempt_id' => $attempt->id,
                ],
            );
        }

        $this->activityRecorded($user);
    }

    private function currentStreak(User $user): int
    {
        $activityDays = $user->learningActivities()
            ->pluck('occurred_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $progressDays = $user->learningProgress()
            ->pluck('last_accessed_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $assessmentDays = $user->assessmentAttempts()
            ->whereNotNull('submitted_at')
            ->pluck('submitted_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $days = $activityDays
            ->merge($progressDays)
            ->merge($assessmentDays)
            ->unique()
            ->sort()
            ->values();

        if ($days->isEmpty()) {
            return 0;
        }

        $daySet = $days->flip();
        $cursor = now()->startOfDay();

        if (! $daySet->has($cursor->toDateString())) {
            $cursor->subDay();
        }

        $streak = 0;

        while ($daySet->has($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }
}
