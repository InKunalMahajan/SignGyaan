<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\CourseMastery;
use App\Models\LearningClass;
use App\Models\User;
use Illuminate\Support\Collection;

class TeacherDashboardData
{
    public function forUser(User $teacher): array
    {
        $classes = $teacher->teachingClasses()
            ->where('is_active', true)
            ->with([
                'learners:id,name,email,is_active',
                'courses' => fn ($query) => $query
                    ->where('courses.is_active', true)
                    ->whereHas('subject', fn ($subject) => $subject->where('is_active', true))
                    ->with('subject:id,name'),
            ])
            ->orderBy('name')
            ->get();

        $learnerIds = $classes
            ->flatMap(fn (LearningClass $class) => $class->learners)
            ->where('is_active', true)
            ->pluck('id')
            ->unique()
            ->values();

        $courseIds = $classes
            ->flatMap(fn (LearningClass $class) => $class->courses)
            ->pluck('id')
            ->unique()
            ->values();

        $masteries = ($learnerIds->isEmpty() || $courseIds->isEmpty())
            ? collect()
            : CourseMastery::query()
                ->whereIn('learner_id', $learnerIds)
                ->whereIn('course_id', $courseIds)
                ->with(['learner:id,name', 'course:id,title'])
                ->get();

        $classCards = $classes->map(function (LearningClass $class) use ($masteries) {
            $classLearnerIds = $class->learners->where('is_active', true)->pluck('id');
            $classCourseIds = $class->courses->pluck('id');
            $classMasteries = $masteries
                ->whereIn('learner_id', $classLearnerIds)
                ->whereIn('course_id', $classCourseIds);
            $average = $classMasteries->isNotEmpty()
                ? round($classMasteries->avg(fn (CourseMastery $mastery) => (float) $mastery->mastery_score), 2)
                : null;

            return [
                'class' => $class,
                'learner_count' => $classLearnerIds->count(),
                'course_count' => $classCourseIds->count(),
                'mastery_average' => $average,
                'url' => route('teacher.classes.show', $class),
                'progress_url' => route('teacher.classes.progress', $class),
            ];
        })->values();

        $assessments = Assessment::query()
            ->where('created_by', $teacher->id)
            ->with('course:id,title')
            ->withCount([
                'attempts as pending_review_count' => fn ($query) => $query->where('status', 'pending_review'),
                'attempts as completed_attempts_count' => fn ($query) => $query->where('status', 'completed'),
            ])
            ->latest('updated_at')
            ->get();

        $assessmentIds = $assessments->pluck('id');
        $attempts = $assessmentIds->isEmpty()
            ? collect()
            : AssessmentAttempt::query()
                ->whereIn('assessment_id', $assessmentIds)
                ->whereIn('status', ['submitted', 'pending_review', 'completed'])
                ->with(['assessment:id,course_id,title,created_by', 'learner:id,name'])
                ->latest('submitted_at')
                ->latest('id')
                ->get();

        $completedAttempts = $attempts->where('status', 'completed');
        $assessmentAverage = $completedAttempts->isNotEmpty()
            ? round($completedAttempts->avg(fn (AssessmentAttempt $attempt) => (float) $attempt->percentage), 2)
            : null;

        $pendingReview = $attempts->where('status', 'pending_review');

        $assessmentCards = $assessments
            ->take(5)
            ->map(fn (Assessment $assessment) => [
                'assessment' => $assessment,
                'url' => route('teacher.assessments.edit', $assessment),
                'attempts_url' => route('teacher.assessments.attempts.index', $assessment),
                'analytics_url' => route('teacher.assessments.analytics', $assessment),
            ])
            ->values();

        $supportLearners = $masteries
            ->whereIn('mastery_level', ['needs_support', 'developing'])
            ->sortBy('mastery_score')
            ->take(6)
            ->map(fn (CourseMastery $mastery) => [
                'learner' => $mastery->learner,
                'course' => $mastery->course,
                'score' => (float) $mastery->mastery_score,
                'level' => $mastery->levelLabel(),
                'updated_at' => $mastery->last_calculated_at,
            ])
            ->values();

        $recentActivity = $this->recentActivity($attempts, $classes);

        return [
            'active_classes' => $classes->count(),
            'active_learners' => $learnerIds->count(),
            'assigned_courses' => $courseIds->count(),
            'pending_reviews' => $pendingReview->count(),
            'published_assessments' => $assessments->where('status', 'published')->count(),
            'draft_assessments' => $assessments->where('status', 'draft')->count(),
            'assessment_average' => $assessmentAverage,
            'mastery_snapshot_average' => $masteries->isNotEmpty()
                ? round($masteries->avg(fn (CourseMastery $mastery) => (float) $mastery->mastery_score), 2)
                : null,
            'classes' => $classCards,
            'assessments' => $assessmentCards,
            'pending_attempts' => $pendingReview->take(5)->values(),
            'support_learners' => $supportLearners,
            'activity' => $recentActivity,
            'has_teaching' => $classes->isNotEmpty(),
        ];
    }

    private function recentActivity(Collection $attempts, Collection $classes): array
    {
        $attemptActivity = $attempts->take(6)->map(function (AssessmentAttempt $attempt) {
            $status = match ($attempt->status) {
                'pending_review' => 'Pending review',
                'completed' => $attempt->passed ? 'Passed' : 'Needs improvement',
                default => 'Submitted',
            };

            return [
                'type' => 'assessment',
                'title' => $attempt->assessment?->title ?? 'Assessment',
                'meta' => ($attempt->learner?->name ?? 'Learner').' · '.$status,
                'when' => ($attempt->reviewed_at ?? $attempt->submitted_at ?? $attempt->updated_at)?->diffForHumans(),
            ];
        });

        if ($attemptActivity->isNotEmpty()) {
            return $attemptActivity->values()->all();
        }

        if ($classes->isNotEmpty()) {
            return $classes->take(4)->map(fn (LearningClass $class) => [
                'type' => 'class',
                'title' => $class->name,
                'meta' => $class->learners->where('is_active', true)->count().' active learners · '.$class->courses->count().' assigned courses',
                'when' => null,
            ])->values()->all();
        }

        return [[
            'type' => 'teaching',
            'title' => 'No teaching activity yet',
            'meta' => 'Create or activate a class to begin your teaching workspace.',
            'when' => null,
        ]];
    }
}
