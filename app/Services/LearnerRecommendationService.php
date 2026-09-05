<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class LearnerRecommendationService
{
    public function __construct(private LearningProgressCatalog $catalog)
    {
    }

    public function nextLessons(User $user, Collection $progressRecords, int $limit = 3): Collection
    {
        return $progressRecords
            ->whereNull('completed_at')
            ->sortByDesc(fn ($progress) => $progress->last_accessed_at?->timestamp ?? 0)
            ->map(function ($progress) {
                $state = $this->catalog->resolve($progress->subject_slug, $progress->course_slug);

                if (! $state || $state['entries']->isEmpty()) {
                    return null;
                }

                $completed = collect($this->catalog->normalizeCompleted(
                    $progress->completed_lessons ?? [],
                    $state['entries']
                ));

                $currentKey = $this->catalog->normalizeKey($progress->current_lesson_key, $state['entries']);
                $currentIndex = $state['entries']->search(
                    fn (array $entry) => $entry['stable_key'] === $currentKey
                );

                $candidate = null;

                if ($currentIndex !== false) {
                    $candidate = $state['entries']
                        ->slice($currentIndex + 1)
                        ->first(fn (array $entry) => ! $completed->contains($entry['stable_key']));
                }

                $candidate ??= $state['entries']
                    ->first(fn (array $entry) => ! $completed->contains($entry['stable_key']));

                if (! $candidate) {
                    return null;
                }

                $lesson = $candidate['lesson'];
                $unit = $candidate['unit'];

                return [
                    'subject' => $state['subject']->name,
                    'course_title' => $state['course']->title,
                    'course_level' => $state['course']->level ?: 'All levels',
                    'unit_title' => $unit->title,
                    'lesson_title' => $lesson->title,
                    'lesson_duration' => $lesson->estimated_duration_minutes,
                    'lesson_key' => $candidate['stable_key'],
                    'course_progress_percent' => $progress->progressPercent(),
                    'reason' => $currentKey === $candidate['stable_key']
                        ? 'Continue your current unfinished lesson.'
                        : 'Recommended next based on your saved course progress.',
                    'url' => route('courses.show', [
                        'subject' => $state['subject']->slug,
                        'course' => $state['course']->slug,
                        'lesson' => $candidate['stable_key'],
                    ]),
                ];
            })
            ->filter()
            ->take($limit)
            ->values();
    }
}
