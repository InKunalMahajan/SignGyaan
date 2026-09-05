<?php

namespace App\Services;

use App\Models\User;

class LearnerCourseProgressService
{
    public function __construct(private LearningProgressCatalog $catalog)
    {
    }

    public function build(User $user, string $subjectSlug, string $courseSlug): ?array
    {
        $state = $this->catalog->resolve($subjectSlug, $courseSlug);

        if (! $state || $state['entries']->isEmpty()) {
            return null;
        }

        $progress = $user->learningProgress()
            ->where('subject_slug', $subjectSlug)
            ->where('course_slug', $courseSlug)
            ->first();

        if ($progress) {
            $progress = $this->catalog->synchronizeRecord($progress, $state);
        }

        $completed = collect($progress?->completed_lessons ?? []);
        $videoProgress = is_array($progress?->video_progress) ? $progress->video_progress : [];
        $currentKey = $progress?->current_lesson_key ?: $state['entries']->first()['stable_key'];

        $units = $state['course']->units->map(function ($unit) use ($state, $completed, $videoProgress, $currentKey) {
            $entries = $state['entries']->where('unit.id', $unit->id)->values();
            $lessons = $entries->map(function (array $entry) use ($completed, $videoProgress, $currentKey) {
                $key = $entry['stable_key'];
                $video = is_array($videoProgress[$key] ?? null) ? $videoProgress[$key] : [];

                return [
                    'id' => $entry['lesson']->id,
                    'key' => $key,
                    'title' => $entry['lesson']->title,
                    'duration' => $entry['lesson']->estimated_duration_minutes,
                    'completed' => $completed->contains($key),
                    'current' => $currentKey === $key,
                    'video_watched_percent' => isset($video['watched_percent']) ? (int) $video['watched_percent'] : null,
                    'url' => route('courses.show', [
                        'subject' => $state['subject']->slug,
                        'course' => $state['course']->slug,
                        'lesson' => $key,
                    ]),
                ];
            });

            $completedCount = $lessons->where('completed', true)->count();
            $total = $lessons->count();

            return [
                'id' => $unit->id,
                'title' => $unit->title,
                'completed_lessons' => $completedCount,
                'total_lessons' => $total,
                'progress_percent' => $total > 0 ? (int) round(($completedCount / $total) * 100) : 0,
                'lessons' => $lessons,
            ];
        })->values();

        $totalLessons = $state['entries']->count();
        $completedCount = $completed->count();

        return [
            'subject' => $state['subject'],
            'course' => $state['course'],
            'progress' => $progress,
            'units' => $units,
            'completed_lessons' => $completedCount,
            'total_lessons' => $totalLessons,
            'progress_percent' => $totalLessons > 0 ? (int) min(100, round(($completedCount / $totalLessons) * 100)) : 0,
            'is_completed' => $totalLessons > 0 && $completedCount >= $totalLessons,
            'resume_url' => route('courses.show', [
                'subject' => $state['subject']->slug,
                'course' => $state['course']->slug,
                'lesson' => $currentKey,
            ]),
        ];
    }
}
