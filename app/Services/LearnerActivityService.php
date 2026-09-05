<?php

namespace App\Services;

use App\Models\LearningActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LearnerActivityService
{
    public function __construct(private AchievementNotificationService $achievements)
    {
    }

    public function record(User $user, array $data, bool $deduplicate = false): LearningActivity
    {
        if ($deduplicate) {
            $existing = $user->learningActivities()
                ->where('activity_type', $data['activity_type'])
                ->when($data['lesson_key'] ?? null, fn ($query, $lessonKey) => $query->where('lesson_key', $lessonKey))
                ->where('occurred_at', '>=', now()->subMinutes(30))
                ->latest('occurred_at')
                ->first();

            if ($existing) {
                $existing->update([
                    'title' => $data['title'],
                    'metadata' => $data['metadata'] ?? $existing->metadata,
                    'occurred_at' => now(),
                ]);

                $this->achievements->activityRecorded($user);

                return $existing;
            }
        }

        $activity = $user->learningActivities()->create([
            'activity_type' => $data['activity_type'],
            'subject_slug' => $data['subject_slug'] ?? null,
            'course_slug' => $data['course_slug'] ?? null,
            'lesson_id' => $data['lesson_id'] ?? null,
            'lesson_key' => $data['lesson_key'] ?? null,
            'title' => $data['title'],
            'metadata' => $data['metadata'] ?? null,
            'occurred_at' => now(),
        ]);

        $this->achievements->activityRecorded($user);

        return $activity;
    }

    public function summary(User $user): array
    {
        $activities = $user->learningActivities()
            ->orderByDesc('occurred_at')
            ->limit(50)
            ->get();

        $activityDays = $user->learningActivities()
            ->pluck('occurred_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $progressDays = $user->learningProgress()
            ->pluck('last_accessed_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $assessmentAttempts = $user->assessmentAttempts()
            ->whereNotNull('submitted_at')
            ->with('assessment.practiceResource.lesson.unit.course.subject')
            ->orderByDesc('submitted_at')
            ->limit(20)
            ->get();

        $assessmentDays = $assessmentAttempts
            ->pluck('submitted_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $days = $activityDays
            ->merge($progressDays)
            ->merge($assessmentDays)
            ->unique()
            ->sort()
            ->values();

        $recentActivity = $activities->map(fn (LearningActivity $activity) => [
            'type' => $activity->activity_type,
            'title' => $activity->title,
            'course_title' => data_get($activity->metadata, 'course_title'),
            'lesson_title' => data_get($activity->metadata, 'lesson_title'),
            'occurred_at' => $activity->occurred_at,
        ]);

        $assessmentActivity = $assessmentAttempts->map(function ($attempt) {
            $resource = $attempt->assessment?->practiceResource;
            $lesson = $resource?->lesson;
            $course = $lesson?->unit?->course;

            return [
                'type' => 'assessment_completed',
                'title' => $resource?->title ?: 'Assessment completed',
                'course_title' => $course?->title,
                'lesson_title' => $lesson?->title,
                'occurred_at' => $attempt->submitted_at,
            ];
        });

        return [
            'current_streak' => $this->currentStreak($days),
            'longest_streak' => $this->longestStreak($days),
            'active_days' => $days->count(),
            'active_today' => $days->contains(now()->toDateString()),
            'recent_activity' => $recentActivity
                ->merge($assessmentActivity)
                ->sortByDesc('occurred_at')
                ->take(8)
                ->values(),
        ];
    }

    private function currentStreak(Collection $days): int
    {
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

    private function longestStreak(Collection $days): int
    {
        if ($days->isEmpty()) {
            return 0;
        }

        $longest = 1;
        $current = 1;
        $previous = null;

        foreach ($days as $day) {
            $date = Carbon::parse($day)->startOfDay();

            if ($previous && $previous->copy()->addDay()->isSameDay($date)) {
                $current++;
            } elseif ($previous) {
                $current = 1;
            }

            $longest = max($longest, $current);
            $previous = $date;
        }

        return $longest;
    }
}
