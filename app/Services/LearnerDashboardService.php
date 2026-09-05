<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;

class LearnerDashboardService
{
    public function __construct(private LearningProgressCatalog $catalog)
    {
    }

    public function build(User $user): array
    {
        $progressRecords = $this->validatedProgressRecords($user);
        $assessmentAttempts = $this->validatedAssessmentAttempts($user);

        $activeCourses = $progressRecords->whereNull('completed_at')->values();
        $completedCourses = $progressRecords->whereNotNull('completed_at')->values();
        $completedLessons = $progressRecords->sum(fn ($progress) => $progress->completedLessonsCount());
        $totalLessons = $progressRecords->sum('total_lessons');
        $overallProgress = $totalLessons > 0
            ? (int) min(100, round(($completedLessons / $totalLessons) * 100))
            : 0;

        $assessmentSummary = $this->assessmentSummary($assessmentAttempts);
        $latestProgress = $activeCourses->first() ?? $progressRecords->first();
        $activeAssessment = $assessmentAttempts->firstWhere('status', 'in-progress');
        $continueLearning = $this->continueLearning($activeCourses);

        return [
            'progressRecords' => $progressRecords,
            'activeCourses' => $activeCourses,
            'completedCourses' => $completedCourses,
            'completedLessons' => $completedLessons,
            'totalTrackedLessons' => $totalLessons,
            'overallProgress' => $overallProgress,
            'assessmentAttempts' => $assessmentAttempts,
            'assessmentSummary' => $assessmentSummary,
            'starterCourses' => $this->starterCourses(),
            'latestProgress' => $latestProgress,
            'activeAssessment' => $activeAssessment,
            'continueLearning' => $continueLearning,
            'primaryContinueLearning' => $continueLearning->first(),
            'dashboardSummary' => [
                'courses_in_progress' => $activeCourses->count(),
                'courses_completed' => $completedCourses->count(),
                'lessons_completed' => $completedLessons,
                'overall_progress' => $overallProgress,
                'assessment_attempts' => $assessmentSummary['total_attempts'],
            ],
        ];
    }

    private function validatedProgressRecords(User $user): Collection
    {
        return $user->learningProgress()
            ->orderByDesc('last_accessed_at')
            ->get()
            ->map(function ($progress) {
                $state = $this->catalog->resolve($progress->subject_slug, $progress->course_slug);

                if (! $state || $state['entries']->isEmpty()) {
                    return null;
                }

                $progress = $this->catalog->synchronizeRecord($progress, $state);
                $currentEntry = $state['entries']->first(
                    fn (array $entry) => $entry['stable_key'] === $progress->current_lesson_key
                );

                $progress->setAttribute('current_lesson_duration', $currentEntry['lesson']->estimated_duration_minutes ?? null);
                $progress->setAttribute('course_level', $state['course']->level ?: 'All levels');

                return $progress;
            })
            ->filter()
            ->values();
    }

    private function continueLearning(Collection $activeCourses): Collection
    {
        return $activeCourses
            ->map(function ($progress) {
                $lessonKey = (string) $progress->current_lesson_key;
                $videoProgress = is_array($progress->video_progress) ? $progress->video_progress : [];
                $video = is_array($videoProgress[$lessonKey] ?? null)
                    ? $videoProgress[$lessonKey]
                    : [];

                return [
                    'subject' => $progress->subject_name,
                    'course_title' => $progress->course_title,
                    'course_level' => $progress->course_level ?: 'All levels',
                    'unit_title' => $progress->current_unit_title,
                    'lesson_title' => $progress->current_lesson_title,
                    'lesson_key' => $lessonKey,
                    'lesson_duration' => $progress->current_lesson_duration,
                    'completed_lessons' => $progress->completedLessonsCount(),
                    'total_lessons' => (int) $progress->total_lessons,
                    'progress_percent' => $progress->progressPercent(),
                    'video_watched_percent' => isset($video['watched_percent'])
                        ? (int) $video['watched_percent']
                        : null,
                    'last_accessed_at' => $progress->last_accessed_at,
                    'resume_url' => route('courses.show', [
                        'subject' => $progress->subject_slug,
                        'course' => $progress->course_slug,
                        'lesson' => $lessonKey,
                    ]),
                ];
            })
            ->values();
    }

    private function validatedAssessmentAttempts(User $user): Collection
    {
        return AssessmentAttempt::query()
            ->where('user_id', $user->id)
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
                                ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published()))))))
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
