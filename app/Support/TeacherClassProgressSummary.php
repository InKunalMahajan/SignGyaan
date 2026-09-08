<?php

namespace App\Support;

use App\Models\LearningClass;
use App\Models\LessonProgress;

class TeacherClassProgressSummary
{
    public function build(LearningClass $class): array
    {
        $class->load([
            'learners' => fn ($query) => $query->orderBy('name'),
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
                                ->where('review_status', 'approved')
                                ->orderBy('position')
                                ->orderBy('id'),
                        ])
                        ->orderBy('position')
                        ->orderBy('id'),
                ])
                ->orderBy('title'),
        ]);

        $lessonEntries = collect();
        $courseLessonIds = $class->courses->mapWithKeys(function ($course) use ($lessonEntries) {
            $ids = collect();

            foreach ($course->units as $unit) {
                foreach ($unit->lessons as $lesson) {
                    $ids->push($lesson->id);
                    if (! $lessonEntries->has($lesson->id)) {
                        $lessonEntries->put($lesson->id, ['lesson' => $lesson, 'unit' => $unit, 'course' => $course]);
                    }
                }
            }

            return [$course->id => $ids->unique()->values()];
        });

        $allLessonIds = $courseLessonIds->flatten()->unique()->values();
        $learnerIds = $class->learners->pluck('id')->values();

        $progressRecords = ($learnerIds->isEmpty() || $allLessonIds->isEmpty()) ? collect() : LessonProgress::query()
            ->whereIn('learner_id', $learnerIds)
            ->whereIn('lesson_id', $allLessonIds)
            ->get();

        $progressByLearner = $progressRecords->groupBy('learner_id');

        $learnerProgress = $class->learners->map(function ($learner) use ($progressByLearner, $courseLessonIds, $class, $allLessonIds) {
            $records = $progressByLearner->get($learner->id, collect())->keyBy('lesson_id');
            $completedCount = $records->where('status', 'completed')->count();
            $startedCount = $records->count();
            $totalLessons = $allLessonIds->count();

            $courses = $class->courses->map(function ($course) use ($records, $courseLessonIds) {
                $lessonIds = $courseLessonIds->get($course->id, collect());
                $total = $lessonIds->count();
                $completed = $lessonIds->filter(fn ($lessonId) => optional($records->get($lessonId))->status === 'completed')->count();

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'subject' => $course->subject?->name,
                    'completed' => $completed,
                    'total' => $total,
                    'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
                ];
            });

            return [
                'learner' => $learner,
                'completed' => $completedCount,
                'in_progress' => max(0, $startedCount - $completedCount),
                'not_started' => max(0, $totalLessons - $startedCount),
                'total' => $totalLessons,
                'percent' => $totalLessons > 0 ? (int) round(($completedCount / $totalLessons) * 100) : 0,
                'courses' => $courses,
            ];
        });

        $recentActivity = $progressRecords->sortByDesc('last_viewed_at')->take(8)->map(function ($progress) use ($lessonEntries, $class) {
            $entry = $lessonEntries->get($progress->lesson_id);
            $learner = $class->learners->firstWhere('id', $progress->learner_id);

            if (! $entry || ! $learner) {
                return null;
            }

            return [
                ...$entry,
                'learner' => $learner,
                'status' => $progress->status,
                'last_viewed_at' => $progress->last_viewed_at,
                'completed_at' => $progress->completed_at,
            ];
        })->filter()->values();

        $averagePercent = $learnerProgress->isNotEmpty() && $allLessonIds->isNotEmpty()
            ? (int) round($learnerProgress->avg('percent'))
            : 0;

        return [
            'class' => $class,
            'course_lesson_ids' => $courseLessonIds,
            'lesson_ids' => $allLessonIds,
            'learner_progress' => $learnerProgress,
            'published_lesson_count' => $allLessonIds->count(),
            'learner_count' => $class->learners->count(),
            'course_count' => $class->courses->count(),
            'average_percent' => $averagePercent,
            'completed_learner_count' => $learnerProgress->filter(fn ($row) => $row['total'] > 0 && $row['percent'] === 100)->count(),
            'needs_attention_count' => $learnerProgress->filter(fn ($row) => $row['total'] > 0 && $row['percent'] < 50)->count(),
            'recent_activity' => $recentActivity,
        ];
    }
}
