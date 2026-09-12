<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\LearnerLearningPath;
use App\Services\MasteryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(Request $request, LearnerLearningPath $learningPath, MasteryService $mastery): View
    {
        $classes = $learningPath->activeClassesFor($request->user());
        $courses = collect();

        foreach ($classes as $class) {
            foreach ($class->courses as $course) {
                if (! $courses->has($course->id)) {
                    $courses->put($course->id, [
                        'class' => $class,
                        'course' => $course,
                        'mastery' => $mastery->calculateFor($request->user(), $course),
                    ]);
                }
            }
        }

        $average = $courses->isNotEmpty()
            ? round($courses->avg(fn ($entry) => (float) $entry['mastery']->mastery_score), 2)
            : 0.0;

        return view('learner.progress.index', [
            'courses' => $courses->values(),
            'averageMastery' => $average,
            'overallLevel' => $mastery->labelFor($average),
        ]);
    }

    public function show(
        Request $request,
        Course $course,
        LearnerLearningPath $learningPath,
        MasteryService $mastery
    ): View {
        $classes = $learningPath->activeClassesFor($request->user());
        $class = $classes->first(fn ($item) => $item->courses->contains('id', $course->id));
        abort_unless($class, 403);

        $course = $learningPath->loadCourseFor($request->user(), $class, $course);
        $snapshot = $mastery->snapshot($request->user(), $course);

        return view('learner.progress.show', [
            'class' => $class,
            'course' => $course,
            'mastery' => $snapshot['mastery'],
            'chapters' => $snapshot['chapters'],
            'recommendations' => $snapshot['recommendations'],
        ]);
    }
}
