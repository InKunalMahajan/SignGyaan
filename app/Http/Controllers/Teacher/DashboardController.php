<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LearningClass;
use App\Support\TeacherClassProgressSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, TeacherClassProgressSummary $progressSummary): View
    {
        $teacher = $request->user();

        $classes = LearningClass::query()
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $classCards = $classes
            ->map(fn (LearningClass $class) => $progressSummary->build($class))
            ->values();

        $activeClassCards = $classCards
            ->filter(fn ($summary) => $summary['class']->is_active)
            ->values();

        $uniqueLearnerIds = $activeClassCards
            ->flatMap(fn ($summary) => $summary['class']->learners->pluck('id'))
            ->unique()
            ->values();

        $uniqueCourseIds = $activeClassCards
            ->flatMap(fn ($summary) => $summary['class']->courses->pluck('id'))
            ->unique()
            ->values();

        $uniqueLessonIds = $activeClassCards
            ->flatMap(fn ($summary) => $summary['lesson_ids'])
            ->unique()
            ->values();

        $classesWithProgress = $activeClassCards
            ->filter(fn ($summary) => $summary['learner_count'] > 0 && $summary['published_lesson_count'] > 0);

        $averageCompletion = $classesWithProgress->isNotEmpty()
            ? (int) round($classesWithProgress->avg('average_percent'))
            : 0;

        $supportSignals = $activeClassCards
            ->flatMap(function ($summary) {
                return $summary['learner_progress']
                    ->filter(fn ($row) => $row['total'] > 0 && $row['percent'] < 50)
                    ->map(fn ($row) => [
                        ...$row,
                        'class' => $summary['class'],
                    ]);
            })
            ->sortBy(fn ($row) => [$row['percent'], $row['learner']->name, $row['class']->name])
            ->values();

        $needsAttentionLearnerCount = $supportSignals
            ->pluck('learner.id')
            ->unique()
            ->count();

        $recentActivity = $activeClassCards
            ->flatMap(function ($summary) {
                return $summary['recent_activity']->map(fn ($activity) => [
                    ...$activity,
                    'class' => $summary['class'],
                ]);
            })
            ->sortByDesc('last_viewed_at')
            ->take(8)
            ->values();

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'classCards' => $classCards,
            'activeClassCount' => $activeClassCards->count(),
            'totalClassCount' => $classCards->count(),
            'uniqueLearnerCount' => $uniqueLearnerIds->count(),
            'activeCourseCount' => $uniqueCourseIds->count(),
            'publishedLessonCount' => $uniqueLessonIds->count(),
            'averageCompletion' => $averageCompletion,
            'needsAttentionLearnerCount' => $needsAttentionLearnerCount,
            'supportSignals' => $supportSignals->take(8)->values(),
            'recentActivity' => $recentActivity,
        ]);
    }
}
