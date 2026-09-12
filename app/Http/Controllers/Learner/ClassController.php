<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningClass;
use App\Services\LearnerDashboardData;
use App\Services\LearnerLearningPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(Request $request, LearnerDashboardData $dashboardData): RedirectResponse|View
    {
        if ($request->boolean('continue')) {
            $data = $dashboardData->forUser($request->user());

            return redirect()->to($data['continue_url']);
        }

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

    public function show(
        Request $request,
        LearningClass $class,
        LearnerLearningPath $learningPath
    ): View {
        $class = $learningPath->loadClassFor($request->user(), $class);

        $allLessonIds = $class->courses
            ->flatMap(fn ($course) => $course->units)
            ->flatMap(fn ($unit) => $unit->lessons)
            ->pluck('id')
            ->values();

        $progressByLesson = $request->user()->lessonProgress()
            ->whereIn('lesson_id', $allLessonIds)
            ->get()
            ->keyBy('lesson_id');

        $courseProgress = $class->courses->mapWithKeys(function ($course) use ($progressByLesson, $class) {
            $lessons = $course->units->flatMap(fn ($unit) => $unit->lessons)->values();
            $lessonIds = $lessons->pluck('id');
            $progress = $progressByLesson->only($lessonIds->all());
            $completedLessonIds = $progress
                ->filter(fn ($item) => $item->status === 'completed')
                ->keys();

            $completedCount = $completedLessonIds->count();
            $totalCount = $lessons->count();
            $recentInProgress = $progress
                ->filter(fn ($item) => $item->status === 'in_progress')
                ->sortByDesc('last_viewed_at')
                ->first();

            $continueLesson = $recentInProgress
                ? $lessons->firstWhere('id', $recentInProgress->lesson_id)
                : $lessons->first(fn ($lesson) => ! $completedLessonIds->contains($lesson->id));

            if ($totalCount > 0 && $completedCount === $totalCount) {
                $state = 'completed';
                $label = 'Completed';
                $actionLabel = 'Review Course';
                $actionUrl = route('learner.classes.courses.show', [$class, $course]);
            } elseif ($progress->isNotEmpty()) {
                $state = 'in_progress';
                $label = 'In progress';
                $actionLabel = 'Resume Learning';
                $actionUrl = $continueLesson
                    ? route('learner.classes.courses.lessons.show', [$class, $course, $continueLesson])
                    : route('learner.classes.courses.show', [$class, $course]);
            } else {
                $state = 'not_started';
                $label = 'Not started';
                $actionLabel = $totalCount > 0 ? 'Start Learning' : 'Open Course';
                $actionUrl = $continueLesson
                    ? route('learner.classes.courses.lessons.show', [$class, $course, $continueLesson])
                    : route('learner.classes.courses.show', [$class, $course]);
            }

            return [
                $course->id => [
                    'state' => $state,
                    'label' => $label,
                    'published_lessons' => $totalCount,
                    'completed_lessons' => $completedCount,
                    'percent' => $totalCount > 0
                        ? (int) round(($completedCount / $totalCount) * 100)
                        : 0,
                    'action_label' => $actionLabel,
                    'action_url' => $actionUrl,
                ],
            ];
        });

        return view('learner.classes.show', [
            'class' => $class,
            'courseProgress' => $courseProgress,
        ]);
    }

    public function course(
        Request $request,
        LearningClass $class,
        Course $course,
        LearnerLearningPath $learningPath
    ): View {
        $course = $learningPath->loadCourseFor($request->user(), $class, $course);

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

        $unitProgress = $course->units->mapWithKeys(function ($unit) use ($progressByLesson) {
            $unitLessonIds = $unit->lessons->pluck('id');
            $unitLessonProgress = $progressByLesson->only($unitLessonIds->all());
            $total = $unit->lessons->count();
            $completed = $unitLessonProgress
                ->filter(fn ($progress) => $progress->status === 'completed')
                ->count();

            if ($total > 0 && $completed === $total) {
                $state = 'completed';
                $label = 'Completed';
            } elseif ($unitLessonProgress->isNotEmpty()) {
                $state = 'in_progress';
                $label = 'In progress';
            } else {
                $state = 'not_started';
                $label = $total > 0 ? 'Not started' : 'No lessons';
            }

            return [
                $unit->id => [
                    'state' => $state,
                    'label' => $label,
                    'completed' => $completed,
                    'total' => $total,
                    'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
                ],
            ];
        });

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
            'unitProgress' => $unitProgress,
            'isCourseCompleted' => $publishedLessonCount > 0
                && $completedLessonCount === $publishedLessonCount,
        ]);
    }
}
