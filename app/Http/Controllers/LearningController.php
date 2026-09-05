<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Services\LearningProgressCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LearningController extends Controller
{
    public function dashboard(Request $request, LearningProgressCatalog $catalog): View
    {
        $progressRecords = $this->validatedProgressRecords($request, $catalog);
        $assessmentAttempts = $this->validatedAssessmentAttempts($request);
        $starterCourses = $this->starterCourses();

        $activeCourses = $progressRecords->whereNull('completed_at')->values();
        $completedCourses = $progressRecords->whereNotNull('completed_at')->values();
        $completedLessons = $progressRecords->sum(fn ($progress) => $progress->completedLessonsCount());
        $totalLessons = $progressRecords->sum('total_lessons');
        $overallProgress = $totalLessons > 0
            ? (int) min(100, round(($completedLessons / $totalLessons) * 100))
            : 0;

        $assessmentSummary = $this->assessmentSummary($assessmentAttempts);

        return view('dashboard', compact(
            'progressRecords',
            'activeCourses',
            'completedCourses',
            'completedLessons',
            'overallProgress',
            'assessmentAttempts',
            'assessmentSummary',
            'starterCourses',
        ));
    }

    public function index(Request $request, LearningProgressCatalog $catalog): View
    {
        $progressRecords = $this->validatedProgressRecords($request, $catalog);
        $assessmentAttempts = $this->validatedAssessmentAttempts($request);
        $starterCourses = $this->starterCourses();

        $activeCourses = $progressRecords->whereNull('completed_at')->values();
        $completedCourses = $progressRecords->whereNotNull('completed_at')->values();
        $completedLessons = $progressRecords->sum(fn ($progress) => $progress->completedLessonsCount());
        $assessmentSummary = $this->assessmentSummary($assessmentAttempts);

        return view('my-learning', compact(
            'progressRecords',
            'activeCourses',
            'completedCourses',
            'completedLessons',
            'assessmentAttempts',
            'assessmentSummary',
            'starterCourses',
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

    private function validatedAssessmentAttempts(Request $request): Collection
    {
        return AssessmentAttempt::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('assessment', fn ($assessmentQuery) => $assessmentQuery
                ->published()
                ->whereHas('practiceResource', fn ($practiceQuery) => $practiceQuery
                    ->published()
                    ->where('kind', 'practice')
                    ->whereIn('resource_type', ['quiz', 'exercise'])
                    ->whereHas('lesson', fn ($lessonQuery) => $lessonQuery
                        ->published()
                        ->whereHas('unit', fn ($unitQuery) => $unitQuery
                            ->published()
                            ->whereHas('course', fn ($courseQuery) => $courseQuery
                                ->published()
                                ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published())))))
            ->with('assessment.practiceResource.lesson.unit.course.subject')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
    }

    private function assessmentSummary(Collection $attempts): array
    {
        $submitted = $attempts->where('status', 'submitted');
        $passed = $submitted->where('passed', true);
        $inProgress = $attempts->where('status', 'in-progress');

        return [
            'total_attempts' => $attempts->count(),
            'submitted' => $submitted->count(),
            'passed' => $passed->count(),
            'in_progress' => $inProgress->count(),
            'best_score' => $submitted->isEmpty()
                ? null
                : round((float) $submitted->max(fn ($attempt) => (float) $attempt->percentage), 2),
            'average_score' => $submitted->isEmpty()
                ? null
                : round((float) $submitted->avg(fn ($attempt) => (float) $attempt->percentage), 2),
        ];
    }

    private function starterCourses(): Collection
    {
        return Course::query()
            ->published()
            ->whereHas('subject', fn ($query) => $query->published())
            ->whereHas('units', fn ($unitQuery) => $unitQuery
                ->published()
                ->whereHas('lessons', fn ($lessonQuery) => $lessonQuery->published()))
            ->with([
                'subject',
                'units' => fn ($unitQuery) => $unitQuery
                    ->published()
                    ->with(['lessons' => fn ($lessonQuery) => $lessonQuery->published()]),
            ])
            ->withCount([
                'units as units_count' => fn ($unitQuery) => $unitQuery->published(),
                'lessons as lessons_count' => fn ($lessonQuery) => $lessonQuery
                    ->published()
                    ->whereHas('unit', fn ($unitQuery) => $unitQuery->published()),
            ])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->limit(3)
            ->get()
            ->map(function (Course $course) {
                $firstLesson = $course->units
                    ->flatMap(fn ($unit) => $unit->lessons)
                    ->first();

                return [
                    'title' => $course->title,
                    'subject' => $course->subject->name,
                    'level' => $course->level ?: 'All levels',
                    'description' => $course->short_description ?: ($course->description ?: 'Structured visual learning with lessons and practice.'),
                    'units' => $course->units_count,
                    'lessons' => $course->lessons_count,
                    'url' => route('courses.show', [
                        'subject' => $course->subject->slug,
                        'course' => $course->slug,
                        'lesson' => 'lesson-'.$firstLesson->id,
                    ]),
                ];
            });
    }
}
