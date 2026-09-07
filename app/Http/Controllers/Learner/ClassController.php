<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\LearningClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $classes = $request->user()
            ->enrolledClasses()
            ->with(['teacher.teacherProfile'])
            ->withCount([
                'courses as active_courses_count' => fn ($query) => $query
                    ->where('courses.is_active', true),
            ])
            ->orderBy('learning_classes.name')
            ->paginate(12);

        return view('learner.classes.index', [
            'classes' => $classes,
        ]);
    }

    public function show(Request $request, LearningClass $class): View
    {
        abort_unless(
            $request->user()->enrolledClasses()->whereKey($class->id)->exists(),
            403
        );

        $class->load([
            'teacher.teacherProfile',
            'courses' => fn ($query) => $query
                ->where('courses.is_active', true)
                ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->where('is_active', true))
                ->with('subject')
                ->orderBy('title'),
        ]);

        return view('learner.classes.show', [
            'class' => $class,
        ]);
    }
}
