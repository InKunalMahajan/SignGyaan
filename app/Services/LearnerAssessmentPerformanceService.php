<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

class LearnerAssessmentPerformanceService
{
    public function build(User $user): array
    {
        $attempts = $this->attempts($user);
        $submitted = $attempts->where('status', 'submitted')->values();
        $passed = $submitted->where('passed', true)->values();
        $inProgress = $attempts->where('status', 'in-progress')->values();

        $bestScore = $submitted->isEmpty() ? null : round((float) $submitted->max(fn ($attempt) => (float) $attempt->percentage), 2);
        $averageScore = $submitted->isEmpty() ? null : round((float) $submitted->avg(fn ($attempt) => (float) $attempt->percentage), 2);
        $passRate = $submitted->isEmpty() ? null : (int) round(($passed->count() / $submitted->count()) * 100);

        $assessmentGroups = $attempts
            ->groupBy('assessment_id')
            ->map(function (Collection $group) {
                $latest = $group->sortByDesc(fn ($attempt) => $attempt->updated_at?->timestamp ?? 0)->first();
                $submitted = $group->where('status', 'submitted');
                $best = $submitted->sortByDesc(fn ($attempt) => (float) $attempt->percentage)->first();
                $assessment = $latest->assessment;
                $practice = $assessment->practiceResource;
                $lesson = $practice->lesson;
                $course = $lesson->unit->course;
                $subject = $course->subject;

                return [
                    'assessment' => $assessment,
                    'title' => $practice->title,
                    'subject' => $subject->name,
                    'course' => $course->title,
                    'lesson' => $lesson->title,
                    'attempts' => $group->count(),
                    'submitted_attempts' => $submitted->count(),
                    'best_score' => $best ? round((float) $best->percentage, 2) : null,
                    'best_passed' => (bool) ($best?->passed ?? false),
                    'latest_status' => $latest->status,
                    'latest_attempt' => $latest,
                    'latest_score' => $latest->status === 'submitted' ? round((float) $latest->percentage, 2) : null,
                    'passing_percentage' => (int) $assessment->passing_percentage,
                    'overview_url' => route('assessments.show', $assessment),
                    'action_url' => $latest->status === 'in-progress'
                        ? route('assessment-attempts.show', [$assessment, $latest])
                        : ($latest->status === 'submitted'
                            ? route('assessment-attempts.result', [$assessment, $latest])
                            : route('assessments.show', $assessment)),
                    'action_label' => match ($latest->status) {
                        'in-progress' => 'Continue Attempt',
                        'submitted' => 'View Latest Result',
                        default => 'Assessment Overview',
                    },
                ];
            })
            ->sortByDesc(fn ($item) => $item['latest_attempt']->updated_at?->timestamp ?? 0)
            ->values();

        $recentResults = $submitted
            ->sortByDesc(fn ($attempt) => $attempt->submitted_at?->timestamp ?? 0)
            ->take(8)
            ->map(function ($attempt) {
                $assessment = $attempt->assessment;
                $practice = $assessment->practiceResource;
                $lesson = $practice->lesson;
                $course = $lesson->unit->course;

                return [
                    'title' => $practice->title,
                    'course' => $course->title,
                    'attempt_number' => $attempt->attempt_number,
                    'score' => round((float) $attempt->percentage, 2),
                    'passed' => (bool) $attempt->passed,
                    'submitted_at' => $attempt->submitted_at,
                    'url' => route('assessment-attempts.result', [$assessment, $attempt]),
                ];
            })
            ->values();

        return [
            'attempts' => $attempts,
            'assessmentGroups' => $assessmentGroups,
            'recentResults' => $recentResults,
            'performanceSummary' => [
                'total_attempts' => $attempts->count(),
                'submitted' => $submitted->count(),
                'passed' => $passed->count(),
                'in_progress' => $inProgress->count(),
                'best_score' => $bestScore,
                'average_score' => $averageScore,
                'pass_rate' => $passRate,
            ],
        ];
    }

    private function attempts(User $user): Collection
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
}
