<?php

namespace App\Services;

use App\Models\ClassCourseAssignment;
use App\Models\ClassEnrollment;
use App\Models\LessonProgress;
use App\Models\User;

class LearnerDashboardData
{
    public function forUser(User $user): array
    {
        $classIds = ClassEnrollment::query()
            ->where('learner_id', $user->id)
            ->pluck('learning_class_id');

        $classCount = $classIds->count();

        $courseCount = $classIds->isEmpty()
            ? 0
            : ClassCourseAssignment::query()
                ->whereIn('learning_class_id', $classIds)
                ->distinct()
                ->count('course_id');

        $completedTotal = LessonProgress::query()
            ->where('learner_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $completedThisMonth = LessonProgress::query()
            ->where('learner_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->startOfMonth())
            ->count();

        $inProgress = LessonProgress::query()
            ->where('learner_id', $user->id)
            ->where('status', 'in_progress')
            ->count();

        $viewedThisWeek = LessonProgress::query()
            ->where('learner_id', $user->id)
            ->whereNotNull('last_viewed_at')
            ->where('last_viewed_at', '>=', now()->subDays(7))
            ->count();

        $recentProgress = LessonProgress::query()
            ->with(['lesson.unit.course'])
            ->where('learner_id', $user->id)
            ->orderByRaw('COALESCE(last_viewed_at, completed_at, updated_at) DESC')
            ->limit(3)
            ->get();

        $updates = $recentProgress->map(function (LessonProgress $progress): array {
            $lesson = $progress->lesson;
            $courseTitle = $lesson?->unit?->course?->title;
            $lessonTitle = $lesson?->title ?? 'Lesson';
            $title = $courseTitle ? "{$courseTitle} · {$lessonTitle}" : $lessonTitle;

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
        })->values()->all();

        if ($updates === []) {
            $updates = [[
                'title' => 'No learning activity yet',
                'meta' => 'Open My Classes to start your first lesson.',
            ]];
        }

        return [
            'stats' => [
                [
                    'label' => 'Enrolled courses',
                    'value' => (string) $courseCount,
                    'helper' => $classCount === 1 ? 'Across 1 enrolled class' : "Across {$classCount} enrolled classes",
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
        ];
    }
}
