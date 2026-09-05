<?php

namespace App\Http\Controllers;

use App\Services\LearningProgressCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LearningController extends Controller
{
    public function dashboard(Request $request, LearningProgressCatalog $catalog): View
    {
        $progressRecords = $this->validatedProgressRecords($request, $catalog);

        $activeCourses = $progressRecords->whereNull('completed_at')->values();
        $completedCourses = $progressRecords->whereNotNull('completed_at')->values();
        $completedLessons = $progressRecords->sum(fn ($progress) => $progress->completedLessonsCount());
        $totalLessons = $progressRecords->sum('total_lessons');
        $overallProgress = $totalLessons > 0
            ? (int) min(100, round(($completedLessons / $totalLessons) * 100))
            : 0;

        return view('dashboard', compact(
            'progressRecords',
            'activeCourses',
            'completedCourses',
            'completedLessons',
            'overallProgress',
        ));
    }

    public function index(Request $request, LearningProgressCatalog $catalog): View
    {
        $progressRecords = $this->validatedProgressRecords($request, $catalog);

        $activeCourses = $progressRecords->whereNull('completed_at')->values();
        $completedCourses = $progressRecords->whereNotNull('completed_at')->values();
        $completedLessons = $progressRecords->sum(fn ($progress) => $progress->completedLessonsCount());

        return view('my-learning', compact(
            'progressRecords',
            'activeCourses',
            'completedCourses',
            'completedLessons',
        ));
    }

    private function validatedProgressRecords(Request $request, LearningProgressCatalog $catalog): Collection
    {
        return $request->user()
            ->learningProgress()
            ->orderByDesc('last_accessed_at')
            ->get()
            ->map(function ($progress) use ($catalog) {
                $state = $catalog->resolve($progress->subject_slug, $progress->course_slug);

                if (! $state || $state['entries']->isEmpty()) {
                    return null;
                }

                return $catalog->synchronizeRecord($progress, $state);
            })
            ->filter()
            ->values();
    }
}
