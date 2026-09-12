<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Collection;

class LearnerDashboardData
{
    public function __construct(
        private LearnerLearningPath $learningPath,
        private MasteryService $mastery,
        private AssessmentAnalyticsService $assessmentAnalytics,
    ) {
    }

    public function forUser(User $user): array
    {
        $classes = $this->learningPath->activeClassesFor($user);
        $lessonEntries = $this->learningPath->lessonEntriesFromClasses($classes);
        $courses = $classes
            ->flatMap(fn ($class) => $class->courses)
            ->unique('id')
            ->values();
        $courseContext = $this->courseContext($classes);
        $lessonIds = $lessonEntries->keys()->values();

        $progressRecords = $lessonIds->isEmpty()
            ? collect()
            : LessonProgress::query()
                ->where('learner_id', $user->id)
                ->whereIn('lesson_id', $lessonIds)
                ->get();

        $progressByLesson = $progressRecords->keyBy('lesson_id');
        $continueEntry = $this->continueEntry($lessonEntries, $progressRecords, $progressByLesson);
        $continue = $this->continueCard($continueEntry, $progressByLesson);
        $completedThisMonth = $progressRecords
            ->where('status', 'completed')
            ->filter(fn (LessonProgress $progress) => $progress->completed_at?->gte(now()->startOfMonth()))
            ->count();

        $courseCards = $courses->map(function (Course $course) use ($user, $courseContext) {
            $mastery = $this->mastery->calculateFor($user, $course);
            $class = $courseContext->get($course->id);

            return [
                'course' => $course,
                'class' => $class,
                'mastery' => $mastery,
                'url' => $class
                    ? route('learner.classes.courses.show', [$class, $course])
                    : route('learner.progress.courses.show', $course),
            ];
        })->values();

        $masteryScores = $courseCards
            ->pluck('mastery.mastery_score')
            ->map(fn ($score) => (float) $score);
        $overallMastery = $masteryScores->isNotEmpty()
            ? round($masteryScores->average(), 2)
            : null;

        $accessibleCourseIds = $courses->pluck('id');
        $publishedAssessments = $accessibleCourseIds->isEmpty()
            ? collect()
            : Assessment::query()
                ->whereIn('course_id', $accessibleCourseIds)
                ->where('status', 'published')
                ->with('course.subject')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get();

        $attempts = AssessmentAttempt::query()
            ->where('learner_id', $user->id)
            ->whereIn('assessment_id', $publishedAssessments->pluck('id'))
            ->with('assessment.course.subject')
            ->latest('updated_at')
            ->get();

        $attemptsByAssessment = $attempts->groupBy('assessment_id');
        $pendingReview = $attempts->where('status', 'pending_review')->count();
        $completedAttempts = $attempts->where('status', 'completed');
        $latestCompleted = $completedAttempts->first();
        $averageAssessment = $completedAttempts->isNotEmpty()
            ? round($completedAttempts->avg(fn ($attempt) => (float) $attempt->percentage), 2)
            : null;

        $assessmentCards = $publishedAssessments
            ->take(4)
            ->map(function (Assessment $assessment) use ($attemptsByAssessment) {
                $attempt = $attemptsByAssessment->get($assessment->id, collect())->first();
                $status = match ($attempt?->status) {
                    'in_progress' => 'In progress',
                    'pending_review' => 'Pending review',
                    'completed' => $attempt->passed === true ? 'Passed' : 'Needs improvement',
                    'submitted' => 'Submitted',
                    default => 'Not started',
                };

                return [
                    'assessment' => $assessment,
                    'attempt' => $attempt,
                    'status' => $status,
                    'url' => $attempt && $attempt->isSubmitted()
                        ? route('learner.assessments.attempts.result', $attempt)
                        : route('learner.assessments.index'),
                ];
            })
            ->values();

        $recommendations = $this->recommendations($user, $courseCards, $courseContext, $lessonEntries);
        $activity = $this->recentActivity($progressRecords, $lessonEntries, $attempts);
        $lessonsCompleted = $progressRecords->where('status', 'completed')->count();
        $lessonsInProgress = $progressRecords->where('status', 'in_progress')->count();

        return [
            'classes_count' => $classes->count(),
            'courses_count' => $courses->count(),
            'lessons_completed' => $lessonsCompleted,
            'lessons_in_progress' => $lessonsInProgress,
            'completed_this_month' => $completedThisMonth,
            'overall_mastery' => $overallMastery,
            'overall_mastery_label' => $overallMastery !== null ? $this->mastery->labelFor($overallMastery) : 'No evidence yet',
            'average_assessment' => $averageAssessment,
            'pending_review' => $pendingReview,
            'latest_assessment' => $latestCompleted,
            'continue' => $continue,
            'courses' => $courseCards,
            'assessments' => $assessmentCards,
            'recommendations' => $recommendations,
            'activity' => $activity,
            'has_learning' => $courses->isNotEmpty() || $lessonEntries->isNotEmpty(),

            // Backward-compatible Phase 4 dashboard contract.
            'continue_url' => $continue['url'],
            'continue_label' => $continue['label'],
            'continue_lesson' => $continue['available'] ? $continue['title'] : null,
            'has_available_lesson' => $continue['available'],
            'stats' => [
                [
                    'label' => 'Enrolled courses',
                    'value' => (string) $courses->count(),
                    'helper' => $classes->count() === 1
                        ? 'Across 1 active class'
                        : 'Across '.$classes->count().' active classes',
                ],
                [
                    'label' => 'Lessons completed',
                    'value' => (string) $lessonsCompleted,
                    'helper' => $completedThisMonth.' completed this month',
                ],
                [
                    'label' => 'Lessons in progress',
                    'value' => (string) $lessonsInProgress,
                    'helper' => 'Current accessible lessons only',
                ],
            ],
            'updates' => $activity,
        ];
    }

    private function courseContext(Collection $classes): Collection
    {
        $context = collect();

        foreach ($classes as $class) {
            foreach ($class->courses as $course) {
                if (! $context->has($course->id)) {
                    $context->put($course->id, $class);
                }
            }
        }

        return $context;
    }

    private function continueCard(?array $entry, Collection $progressByLesson): array
    {
        if (! $entry) {
            return [
                'available' => false,
                'label' => 'Open My Classes',
                'title' => 'No lesson ready yet',
                'meta' => 'Your published lessons will appear here when available.',
                'url' => route('learner.classes.index'),
            ];
        }

        $progress = $progressByLesson->get($entry['lesson']->id);

        return [
            'available' => true,
            'label' => $progress?->status === 'in_progress' ? 'Resume Lesson' : 'Start Learning',
            'title' => $entry['lesson']->title,
            'course' => $entry['course']->title,
            'class' => $entry['class']->name,
            'meta' => $progress?->status === 'in_progress'
                ? 'Continue where you stopped.'
                : 'Next available published lesson.',
            'url' => route('learner.classes.courses.lessons.show', [
                $entry['class'],
                $entry['course'],
                $entry['lesson'],
            ]),
        ];
    }

    private function recommendations(
        User $user,
        Collection $courseCards,
        Collection $courseContext,
        Collection $lessonEntries
    ): array {
        $items = collect();

        foreach ($courseCards as $card) {
            $snapshot = $this->mastery->snapshot($user, $card['course']);
            $class = $courseContext->get($card['course']->id);

            foreach ($snapshot['recommendations'] as $recommendation) {
                $url = route('learner.progress.courses.show', $card['course']);

                if (($recommendation['type'] ?? null) === 'lesson' && $class && isset($recommendation['lesson_id'])) {
                    $entry = $lessonEntries->get($recommendation['lesson_id']);
                    if ($entry) {
                        $url = route('learner.classes.courses.lessons.show', [
                            $entry['class'],
                            $entry['course'],
                            $entry['lesson'],
                        ]);
                    }
                } elseif (in_array($recommendation['type'] ?? '', ['assessment', 'assessment_review'], true)) {
                    $url = route('learner.assessments.index');
                }

                $items->push(array_merge($recommendation, [
                    'course' => $card['course']->title,
                    'url' => $url,
                ]));
            }
        }

        return $items->take(4)->values()->all();
    }

    private function recentActivity(Collection $progressRecords, Collection $lessonEntries, Collection $attempts): array
    {
        $lessonActivity = $progressRecords->map(function (LessonProgress $progress) use ($lessonEntries): ?array {
            $entry = $lessonEntries->get($progress->lesson_id);
            if (! $entry) {
                return null;
            }

            $date = $progress->completed_at ?? $progress->last_viewed_at ?? $progress->updated_at;

            return [
                'type' => 'lesson',
                'title' => $entry['lesson']->title,
                'meta' => $progress->status === 'completed'
                    ? $entry['course']->title.' · Completed'
                    : $entry['course']->title.' · In progress',
                'date' => $date,
            ];
        })->filter();

        $assessmentActivity = $attempts
            ->whereIn('status', ['pending_review', 'completed'])
            ->map(fn ($attempt) => [
                'type' => 'assessment',
                'title' => $attempt->assessment?->title ?? 'Assessment',
                'meta' => $attempt->status === 'pending_review'
                    ? 'Submitted · Pending teacher review'
                    : (($attempt->percentage !== null ? number_format((float) $attempt->percentage, 2).'%' : 'Completed').' · '.($attempt->passed ? 'Passed' : 'Needs improvement')),
                'date' => $attempt->reviewed_at ?? $attempt->submitted_at ?? $attempt->updated_at,
            ]);

        $activity = $lessonActivity
            ->concat($assessmentActivity)
            ->sortByDesc('date')
            ->take(6)
            ->map(function ($item) {
                $item['when'] = $item['date']?->diffForHumans();
                unset($item['date']);

                return $item;
            })
            ->values()
            ->all();

        return $activity !== [] ? $activity : [[
            'type' => 'learning',
            'title' => 'No activity yet',
            'meta' => 'Start your first lesson or assessment to see activity here.',
            'when' => null,
        ]];
    }

    private function continueEntry(
        Collection $lessonEntries,
        Collection $progressRecords,
        Collection $progressByLesson
    ): ?array {
        $recentInProgress = $progressRecords
            ->where('status', 'in_progress')
            ->sortByDesc(fn (LessonProgress $progress) => $progress->last_viewed_at ?? $progress->updated_at)
            ->first(fn (LessonProgress $progress) => $lessonEntries->has($progress->lesson_id));

        if ($recentInProgress) {
            return $lessonEntries->get($recentInProgress->lesson_id);
        }

        return $lessonEntries->first(function (array $entry) use ($progressByLesson): bool {
            return $progressByLesson->get($entry['lesson']->id)?->status !== 'completed';
        });
    }
}
