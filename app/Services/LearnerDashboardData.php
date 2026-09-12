<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Collection;

class LearnerDashboardData
{
    public function forUser(User $user): array
    {
        $classes = $user->enrolledClasses()
            ->where('learning_classes.is_active', true)
            ->with([
                'courses' => fn ($query) => $query
                    ->where('courses.is_active', true)
                    ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->where('is_active', true))
                    ->with([
                        'subject',
                        'units' => fn ($unitQuery) => $unitQuery
                            ->where('is_active', true)
                            ->with([
                                'lessons' => fn ($lessonQuery) => $lessonQuery
                                    ->where('status', 'published')
                                    ->orderBy('position')
                                    ->orderBy('id'),
                            ])
                            ->orderBy('position')
                            ->orderBy('id'),
                    ])
                    ->orderBy('title'),
            ])
            ->orderBy('learning_classes.name')
            ->get();

        $courseIds = collect();
        $lessonEntries = collect();

        foreach ($classes as $class) {
            foreach ($class->courses as $course) {
                $courseIds->push($course->id);

                foreach ($course->units as $unit) {
                    foreach ($unit->lessons as $lesson) {
                        if (! $lessonEntries->has($lesson->id)) {
                            $lessonEntries->put($lesson->id, [
                                'class' => $class,
                                'course' => $course,
                                'unit' => $unit,
                                'lesson' => $lesson,
                            ]);
                        }
                    }
                }
            }
        }

        $courseCount = $courseIds->unique()->count();
        $lessonIds = $lessonEntries->keys()->values();

        $progressRecords = $lessonIds->isEmpty()
            ? collect()
            : LessonProgress::query()
                ->where('learner_id', $user->id)
                ->whereIn('lesson_id', $lessonIds)
                ->get();

        $progressByLesson = $progressRecords->keyBy('lesson_id');
        $completedTotal = $progressRecords->where('status', 'completed')->count();
        $inProgress = $progressRecords->where('status', 'in_progress')->count();

        $completedThisMonth = $progressRecords
            ->where('status', 'completed')
            ->filter(fn (LessonProgress $progress) => $progress->completed_at?->gte(now()->startOfMonth()))
            ->count();

        $viewedThisWeek = $progressRecords
            ->filter(fn (LessonProgress $progress) => $progress->last_viewed_at?->gte(now()->subDays(7)))
            ->count();

        $continueEntry = $this->continueEntry($lessonEntries, $progressRecords, $progressByLesson);
        $continueUrl = $continueEntry
            ? route('learner.classes.courses.lessons.show', [
                $continueEntry['class'],
                $continueEntry['course'],
                $continueEntry['lesson'],
            ])
            : route('learner.classes.index');

        $continueProgress = $continueEntry
            ? $progressByLesson->get($continueEntry['lesson']->id)
            : null;

        $updates = $progressRecords
            ->sortByDesc(fn (LessonProgress $progress) => $progress->last_viewed_at ?? $progress->completed_at ?? $progress->updated_at)
            ->take(3)
            ->map(function (LessonProgress $progress) use ($lessonEntries): ?array {
                $entry = $lessonEntries->get($progress->lesson_id);

                if (! $entry) {
                    return null;
                }

                $lesson = $entry['lesson'];
                $course = $entry['course'];
                $title = "{$course->title} · {$lesson->title}";

                if ($progress->status === 'completed') {
                    $when = $progress->completed_at?->diffForHumans();
                    $meta = $when ? "Completed {$when}" : 'Completed';
                } else {
                    $when = $progress->last_viewed_at?->diffForHumans();
                    $meta = $when ? "Last viewed {$when}" : 'In progress';
                }

                return [
                    'title' => $title,
                    'meta' => $meta,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if ($updates === []) {
            $updates = [[
                'title' => 'No learning activity yet',
                'meta' => $lessonEntries->isEmpty()
                    ? 'No published lessons are available in your active classes yet.'
                    : 'Continue Learning to start your first available lesson.',
            ]];
        }

        return [
            'stats' => [
                [
                    'label' => 'Enrolled courses',
                    'value' => (string) $courseCount,
                    'helper' => $classes->count() === 1
                        ? 'Across 1 active class'
                        : 'Across '.$classes->count().' active classes',
                ],
                [
                    'label' => 'Lessons completed',
                    'value' => (string) $completedTotal,
                    'helper' => "{$completedThisMonth} completed this month",
                ],
                [
                    'label' => 'Lessons in progress',
                    'value' => (string) $inProgress,
                    'helper' => "{$viewedThisWeek} viewed in the last 7 days",
                ],
            ],
            'updates' => $updates,
            'continue_url' => $continueUrl,
            'continue_label' => $continueProgress?->status === 'in_progress' ? 'Resume Lesson' : 'Start Learning',
            'continue_lesson' => $continueEntry ? $continueEntry['lesson']->title : null,
            'has_available_lesson' => (bool) $continueEntry,
        ];
    }

    private function continueEntry(
        Collection $lessonEntries,
        Collection $progressRecords,
        Collection $progressByLesson
    ): ?array {
        $recentInProgress = $progressRecords
            ->where('status', 'in_progress')
            ->sortByDesc(fn (LessonProgress $progress) => $progress->last_viewed_at ?? $progress->updated_at)
            ->first(fn (LessonProgress $progress) => $lessonEntries->has($progress->lesson_id));

        if ($recentInProgress) {
            return $lessonEntries->get($recentInProgress->lesson_id);
        }

        return $lessonEntries->first(function (array $entry) use ($progressByLesson): bool {
            return $progressByLesson->get($entry['lesson']->id)?->status !== 'completed';
        });
    }
}
