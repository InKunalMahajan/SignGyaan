<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningClass;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArchitectureController extends Controller
{
    public function teaching(): View
    {
        $classes = LearningClass::query()
            ->with('teacher')
            ->withCount(['learners', 'courses'])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.architecture.teaching', [
            'classes' => $classes,
            'stats' => [
                'teachers' => User::where('role', 'teacher')->where('is_active', true)->count(),
                'classes' => LearningClass::count(),
                'active_classes' => LearningClass::where('is_active', true)->count(),
                'enrollments' => DB::table('class_enrollments')->count(),
                'course_assignments' => DB::table('class_course_assignments')->count(),
            ],
        ]);
    }

    public function progress(): View
    {
        $total = LessonProgress::count();
        $completed = LessonProgress::where('status', 'completed')->count();
        $inProgress = LessonProgress::where('status', 'in_progress')->count();

        $recent = LessonProgress::query()
            ->with(['learner', 'lesson'])
            ->latest('updated_at')
            ->limit(20)
            ->get();

        return view('admin.architecture.progress', [
            'recent' => $recent,
            'stats' => [
                'learners' => User::where('role', 'learner')->where('is_active', true)->count(),
                'records' => $total,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            ],
        ]);
    }
}
