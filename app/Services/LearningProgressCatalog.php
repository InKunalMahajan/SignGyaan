<?php

namespace App\Services;

use App\Models\Course;
use App\Models\LearningProgress;
use Illuminate\Support\Collection;

class LearningProgressCatalog
{
    public function resolve(string $subjectSlug, string $courseSlug): ?array
    {
        $course = Course::query()
            ->published()
            ->where('slug', $courseSlug)
            ->whereHas('subject', fn ($query) => $query
                ->published()
                ->where('slug', $subjectSlug))
            ->with([
                'subject',
                'units' => fn ($unitQuery) => $unitQuery
                    ->published()
                    ->with([
                        'lessons' => fn ($lessonQuery) => $lessonQuery
                            ->published()
                            ->orderBy('sort_order')
                            ->orderBy('title'),
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('title'),
            ])
            ->first();

        if (! $course || ! $course->subject?->is_published) {
            return null;
        }

        $entries = collect();

        foreach ($course->units as $unitIndex => $unit) {
            $unitNumber = $unitIndex + 1;

            foreach ($unit->lessons as $lessonIndex => $lesson) {
                $lessonNumber = $lessonIndex + 1;

                $entries->push([
                    'lesson' => $lesson,
                    'unit' => $unit,
                    'stable_key' => 'lesson-'.$lesson->id,
                    'legacy_key' => 'unit-'.$unitNumber.'-lesson-'.$lessonNumber,
                ]);
            }
        }

        return [
            'subject' => $course->subject,
            'course' => $course,
            'entries' => $entries,
        ];
    }

    public function normalizeKey(?string $key, Collection $entries): ?string
    {
        $key = trim((string) $key);

        if ($key === '') {
            return null;
        }

        $entry = $entries->first(
            fn (array $entry) => $entry['stable_key'] === $key || $entry['legacy_key'] === $key
        );

        return $entry['stable_key'] ?? null;
    }

    public function normalizeCompleted(array $keys, Collection $entries): array
    {
        return collect($keys)
            ->map(fn ($key) => $this->normalizeKey(is_string($key) ? $key : null, $entries))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function synchronizeRecord(LearningProgress $progress, array $state): LearningProgress
    {
        $entries = $state['entries'];
        $completed = $this->normalizeCompleted($progress->completed_lessons ?? [], $entries);
        $currentKey = $this->normalizeKey($progress->current_lesson_key, $entries);

        if (! $currentKey) {
            $currentKey = $entries
                ->first(fn (array $entry) => ! in_array($entry['stable_key'], $completed, true))['stable_key']
                ?? $entries->first()['stable_key']
                ?? null;
        }

        $totalLessons = $entries->count();

        $progress->setAttribute('subject_name', $state['subject']->name);
        $progress->setAttribute('course_title', $state['course']->title);
        $progress->setAttribute('total_lessons', $totalLessons);
        $progress->setAttribute('current_lesson_key', $currentKey);
        $progress->setAttribute('completed_lessons', $completed);
        $progress->setAttribute(
            'completed_at',
            $totalLessons > 0 && count($completed) >= $totalLessons
                ? ($progress->completed_at ?? $progress->updated_at ?? now())
                : null
        );

        return $progress;
    }
}
