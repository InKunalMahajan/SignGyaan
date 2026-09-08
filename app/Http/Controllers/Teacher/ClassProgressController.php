<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LearningClass;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassProgressController extends Controller
{
    public function show(Request $request, LearningClass $class): View
    {
        abort_unless($class->teacher_id === $request->user()->id, 403);

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
                                ->orderBy('position')
                                ->orderBy('id'),
                        ])
                        ->orderBy('position')
                        ->orderBy('id'),
                ])
                ->orderBy('title'),
        ]);

        $courseLessonIds = $class->courses->mapWithKeys(function ($course) {
            return [$course->id => $course->units->flatMap(fn ($unit) => $unit->lessons)->pluck('id')->values()];
        });
        $allLessonIds = $courseLessonIds->flatten()->unique()->values();
        $learnerIds = $class->learners->pluck('id');

        $progressByLearner = LessonProgress::query()
            ->whereIn('learner_id', $learnerIds)
            ->whereIn('lesson_id', $allLessonIds)
            ->get()
            ->groupBy('learner_id');

        $learnerProgress = $class->learners->map(function ($learner) use ($progressByLearner, $courseLessonIds, $class, $allLessonIds) {
            $records = $progressByLearner->get($learner->id, collect())->keyBy('lesson_id');
            $completedCount = $records->where('status', 'completed')->count();
            $startedCount = $records->count();
            $totalLessons = $allLessonIds->count();

            $courses = $class->courses->map(function ($course) use ($records, $courseLessonIds) {
                $lessonIds = $courseLessonIds->get($course->id, collect());
                $total = $lessonIds->count();
                $completed = $lessonIds
                    ->filter(fn ($lessonId) => optional($records->get($lessonId))->status === 'completed')
                    ->count();

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

        return view('teacher.classes.progress', [
            'class' => $class,
            'learnerProgress' => $learnerProgress,
            'publishedLessonCount' => $allLessonIds->count(),
            'averagePercent' => $learnerProgress->isNotEmpty()
                ? (int) round($learnerProgress->avg('percent'))
                : 0,
        ]);
    }
}
