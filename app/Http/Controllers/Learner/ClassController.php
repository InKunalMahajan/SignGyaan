<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
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
                    ->where('courses.is_active', true)
                    ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->where('is_active', true)),
            ])
            ->orderBy('learning_classes.name')
            ->paginate(12);

        return view('learner.classes.index', [
            'classes' => $classes,
        ]);
    }

    public function show(Request $request, LearningClass $class): View
    {
        $this->authorizeEnrollment($request, $class);

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

    public function course(Request $request, LearningClass $class, Course $course): View
    {
        $this->authorizeEnrollment($request, $class);

        abort_unless(
            $class->courses()
                ->whereKey($course->id)
                ->where('courses.is_active', true)
                ->exists(),
            403
        );

        $course->load([
            'subject',
            'units' => fn ($query) => $query
                ->where('is_active', true)
                ->with([
                    'lessons' => fn ($lessonQuery) => $lessonQuery
                        ->where('status', 'published')
                        ->orderBy('position')
                        ->orderBy('id'),
                ])
                ->orderBy('position')
                ->orderBy('id'),
        ]);

        abort_unless($course->subject?->is_active, 404);

        $lessons = $course->units->flatMap(fn ($unit) => $unit->lessons)->values();
        $lessonIds = $lessons->pluck('id');
        $progressByLesson = $request->user()->lessonProgress()
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');

        $completedLessonIds = $progressByLesson
            ->filter(fn ($progress) => $progress->status === 'completed')
            ->keys();
        $completedLessonCount = $completedLessonIds->count();
        $publishedLessonCount = $lessons->count();

        $recentInProgress = $progressByLesson
            ->filter(fn ($progress) => $progress->status === 'in_progress')
            ->sortByDesc('last_viewed_at')
            ->first();

        $continueLesson = $recentInProgress
            ? $lessons->firstWhere('id', $recentInProgress->lesson_id)
            : $lessons->first(fn ($lesson) => ! $completedLessonIds->contains($lesson->id));

        return view('learner.classes.course', [
            'class' => $class->loadMissing('teacher.teacherProfile'),
            'course' => $course,
            'publishedLessonCount' => $publishedLessonCount,
            'completedLessonCount' => $completedLessonCount,
            'progressPercent' => $publishedLessonCount > 0
                ? (int) round(($completedLessonCount / $publishedLessonCount) * 100)
                : 0,
            'progressByLesson' => $progressByLesson,
            'continueLesson' => $continueLesson,
        ]);
    }

    private function authorizeEnrollment(Request $request, LearningClass $class): void
    {
        abort_unless(
            $request->user()->enrolledClasses()->whereKey($class->id)->exists(),
            403
        );
    }
}
