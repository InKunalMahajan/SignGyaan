<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MasteryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(Request $request, MasteryService $mastery): View
    {
        $classes = $request->user()->teachingClasses()
            ->where('is_active', true)
            ->with(['learners', 'courses.subject'])
            ->orderBy('name')
            ->get();

        $rows = collect();
        foreach ($classes as $class) {
            foreach ($class->learners as $learner) {
                foreach ($class->courses->where('is_active', true) as $course) {
                    if (! $course->subject?->is_active) {
                        continue;
                    }

                    $rows->push([
                        'class' => $class,
                        'learner' => $learner,
                        'course' => $course,
                        'mastery' => $mastery->calculateFor($learner, $course),
                    ]);
                }
            }
        }

        return view('teacher.progress.index', [
            'classes' => $classes,
            'rows' => $rows,
        ]);
    }

    public function show(Request $request, User $learner, MasteryService $mastery): View
    {
        abort_unless($learner->role === 'learner', 404);

        $classes = $request->user()->teachingClasses()
            ->where('is_active', true)
            ->whereHas('learners', fn ($query) => $query->whereKey($learner->id))
            ->with(['courses.subject'])
            ->orderBy('name')
            ->get();

        abort_if($classes->isEmpty(), 403);

        $courses = collect();
        foreach ($classes as $class) {
            foreach ($class->courses->where('is_active', true) as $course) {
                if (! $course->subject?->is_active || $courses->has($course->id)) {
                    continue;
                }

                $snapshot = $mastery->snapshot($learner, $course);
                $courses->put($course->id, [
                    'class' => $class,
                    'course' => $course,
                    'mastery' => $snapshot['mastery'],
                    'chapters' => $snapshot['chapters'],
                    'recommendations' => $snapshot['recommendations'],
                ]);
            }
        }

        return view('teacher.progress.show', [
            'learner' => $learner,
            'courses' => $courses->values(),
        ]);
    }
}
