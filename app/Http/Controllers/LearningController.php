<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningController extends Controller
{
    public function dashboard(Request $request): View
    {
        $progressRecords = $request->user()
            ->learningProgress()
            ->orderByDesc('last_accessed_at')
            ->get();

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

    public function index(Request $request): View
    {
        $progressRecords = $request->user()
            ->learningProgress()
            ->orderByDesc('last_accessed_at')
            ->get();

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
}
