<?php

namespace App\Support;

use App\Models\LessonProgress;
use App\Models\User;

class LearnerProgressSummary
{
    public function build(User $learner): array
    {
        $classes = $learner->enrolledClasses()
            ->with([
                'teacher.teacherProfile',
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
            ])
            ->orderByDesc('learning_classes.is_active')
            ->orderBy('learning_classes.name')
            ->get();

        $courseEntries = collect();
        $lessonEntries = collect();

        foreach ($classes as $class) {
            foreach ($class->courses as $course) {
                $courseLessonIds = $course->units->flatMap(fn ($unit) => $unit->lessons)->pluck('id')->unique()->values();

                if (! $courseEntries->has($course->id)) {
                    $courseEntries->put($course->id, ['course' => $course, 'class' => $class, 'lesson_ids' => $courseLessonIds]);
                }

                foreach ($course->units as $unit) {
                    foreach ($unit->lessons as $lesson) {
                        if (! $lessonEntries->has($lesson->id)) {
                            $lessonEntries->put($lesson->id, ['lesson' => $lesson, 'unit' => $unit, 'course' => $course, 'class' => $class]);
                        }
                    }
                }
            }
        }

        $lessonIds = $lessonEntries->keys()->values();
        $progressRecords = $lessonIds->isEmpty() ? collect() : LessonProgress::query()
            ->where('learner_id', $learner->id)
            ->whereIn('lesson_id', $lessonIds)
            ->get();

        $progressByLesson = $progressRecords->keyBy('lesson_id');
        $completedLessonCount = $progressRecords->where('status', 'completed')->count();
        $startedLessonCount = $progressRecords->count();
        $totalLessonCount = $lessonIds->count();
        $overallPercent = $totalLessonCount > 0 ? (int) round(($completedLessonCount / $totalLessonCount) * 100) : 0;

        $courseCards = $courseEntries->map(function (array $entry) use ($progressByLesson) {
            $lessonIds = $entry['lesson_ids'];
            $total = $lessonIds->count();
            $records = $lessonIds->map(fn ($lessonId) => $progressByLesson->get($lessonId))->filter();
            $completed = $records->where('status', 'completed')->count();
            $started = $records->count();
            $percent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

            return [
                ...$entry,
                'total' => $total,
                'completed' => $completed,
                'started' => $started,
                'percent' => $percent,
                'state' => match (true) {
                    $total > 0 && $completed === $total => 'Completed',
                    $started > 0 => 'In progress',
                    default => 'Not started',
                },
                'last_viewed_at' => $records->max('last_viewed_at'),
            ];
        })->sortBy(function ($item) {
            $rank = $item['state'] === 'In progress' ? 0 : ($item['state'] === 'Not started' ? 1 : 2);
            return sprintf('%d|%s', $rank, strtolower($item['course']->title));
        })->values();

        $recentActivity = $progressRecords->sortByDesc('last_viewed_at')->take(5)->map(function ($progress) use ($lessonEntries) {
            $entry = $lessonEntries->get($progress->lesson_id);
            if (! $entry) {
                return null;
            }

            return [
                ...$entry,
                'status' => $progress->status,
                'last_viewed_at' => $progress->last_viewed_at,
                'completed_at' => $progress->completed_at,
            ];
        })->filter()->values();

        return [
            'classes' => $classes,
            'active_class_count' => $classes->where('is_active', true)->count(),
            'total_class_count' => $classes->count(),
            'course_cards' => $courseCards,
            'course_count' => $courseCards->count(),
            'in_progress_course_count' => $courseCards->where('state', 'In progress')->count(),
            'completed_course_count' => $courseCards->where('state', 'Completed')->count(),
            'completed_lesson_count' => $completedLessonCount,
            'started_lesson_count' => $startedLessonCount,
            'total_lesson_count' => $totalLessonCount,
            'overall_percent' => $overallPercent,
            'recent_activity' => $recentActivity,
        ];
    }
}
