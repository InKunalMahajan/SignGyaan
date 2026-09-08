<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use App\Support\TeacherClassProgressSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, TeacherClassProgressSummary $progressSummary): View
    {
        $classes = LearningClass::query()
            ->with('teacher')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $classCards = $classes->map(fn (LearningClass $class) => $progressSummary->build($class))->values();
        $activeClassCards = $classCards->filter(fn ($summary) => $summary['class']->is_active)->values();

        $roleCounts = collect(User::ROLES)->mapWithKeys(fn ($role) => [
            $role => User::query()->where('role', $role)->count(),
        ]);

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
            ->flatMap(fn ($summary) => $summary['learner_progress']
                ->filter(fn ($row) => $row['total'] > 0 && $row['percent'] < 50)
                ->map(fn ($row) => [...$row, 'class' => $summary['class']]))
            ->sortBy(fn ($row) => [$row['percent'], $row['learner']->name])
            ->values();

        $recentActivity = $activeClassCards
            ->flatMap(fn ($summary) => $summary['recent_activity']->map(fn ($activity) => [
                ...$activity,
                'class' => $summary['class'],
            ]))
            ->unique(fn ($activity) => $activity['learner']->id.':'.$activity['lesson']->id)
            ->sortByDesc('last_viewed_at')
            ->take(10)
            ->values();

        return view('admin.dashboard', [
            'admin' => $request->user(),
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'inactiveUsers' => User::where('is_active', false)->count(),
            'roleCounts' => $roleCounts,
            'totalClasses' => $classes->count(),
            'activeClassCount' => $activeClassCards->count(),
            'uniqueLearnerCount' => $uniqueLearnerIds->count(),
            'activeAssignedCourseCount' => $uniqueCourseIds->count(),
            'activeCurriculumLessonCount' => $uniqueLessonIds->count(),
            'averageCompletion' => $averageCompletion,
            'needsAttentionLearnerCount' => $supportSignals->pluck('learner.id')->unique()->count(),
            'totalSubjects' => Subject::count(),
            'activeSubjects' => Subject::where('is_active', true)->count(),
            'totalCourses' => Course::count(),
            'activeCourses' => Course::where('is_active', true)->count(),
            'totalLessons' => Lesson::count(),
            'publishedLessons' => Lesson::where('status', 'published')->count(),
            'draftLessons' => Lesson::where('status', 'draft')->count(),
            'classCards' => $classCards,
            'supportSignals' => $supportSignals->take(10)->values(),
            'recentActivity' => $recentActivity,
        ]);
    }
}
