<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

class AssessmentAnalyticsService
{
    public function learnerHistory(User $learner): Collection
    {
        return AssessmentAttempt::query()
            ->where('learner_id', $learner->id)
            ->whereIn('status', ['submitted', 'pending_review', 'completed'])
            ->with(['assessment.course.subject'])
            ->latest('submitted_at')
            ->latest('id')
            ->get();
    }

    public function assessmentSummary(Assessment $assessment): array
    {
        $attempts = $assessment->attempts()
            ->whereIn('status', ['submitted', 'pending_review', 'completed'])
            ->get();

        $completed = $attempts->where('status', 'completed');
        $passed = $completed->where('passed', true)->count();
        $completedCount = $completed->count();
        $percentages = $completed
            ->pluck('percentage')
            ->filter(fn ($value) => $value !== null)
            ->map(fn ($value) => (float) $value);
        $marks = $completed
            ->pluck('earned_marks')
            ->filter(fn ($value) => $value !== null)
            ->map(fn ($value) => (float) $value);

        return [
            'appeared' => $attempts->count(),
            'completed' => $completedCount,
            'pending_review' => $attempts->where('status', 'pending_review')->count(),
            'passed' => $passed,
            'failed' => max(0, $completedCount - $passed),
            'pass_percentage' => $completedCount > 0 ? round(($passed / $completedCount) * 100, 2) : null,
            'average_percentage' => $percentages->isNotEmpty() ? round($percentages->average(), 2) : null,
            'average_marks' => $marks->isNotEmpty() ? round($marks->average(), 2) : null,
            'highest_percentage' => $percentages->isNotEmpty() ? round($percentages->max(), 2) : null,
            'lowest_percentage' => $percentages->isNotEmpty() ? round($percentages->min(), 2) : null,
        ];
    }

    public function questionAnalysis(Assessment $assessment): Collection
    {
        $assessment->loadMissing('questions');
        $attemptIds = $assessment->attempts()
            ->whereIn('status', ['submitted', 'pending_review', 'completed'])
            ->pluck('id');

        $answersByQuestion = AssessmentAnswer::query()
            ->whereIn('attempt_id', $attemptIds)
            ->get()
            ->groupBy('question_id');

        return $assessment->questions->map(function ($question) use ($answersByQuestion) {
            $answers = $answersByQuestion->get($question->id, collect());
            $scored = $answers->filter(fn ($answer) => ! $answer->requires_review && $answer->awarded_marks !== null);
            $correct = $scored->where('is_correct', true)->count();
            $incorrect = $scored->where('is_correct', false)->count();
            $pending = $answers->where('requires_review', true)->count();
            $awarded = $scored->pluck('awarded_marks')->map(fn ($value) => (float) $value);

            return [
                'question_id' => $question->id,
                'position' => $question->position,
                'prompt' => $question->prompt,
                'type' => $question->type,
                'marks' => (float) $question->marks,
                'responses' => $answers->count(),
                'scored' => $scored->count(),
                'correct' => $correct,
                'incorrect' => $incorrect,
                'pending_review' => $pending,
                'accuracy_percentage' => $scored->count() > 0
                    ? round(($correct / $scored->count()) * 100, 2)
                    : null,
                'average_marks' => $awarded->isNotEmpty() ? round($awarded->average(), 2) : null,
            ];
        })->values();
    }

    public function masterySignalsForLearner(User $learner): Collection
    {
        return AssessmentAttempt::query()
            ->where('learner_id', $learner->id)
            ->where('status', 'completed')
            ->with('assessment:id,course_id')
            ->oldest('submitted_at')
            ->oldest('id')
            ->get()
            ->map(fn (AssessmentAttempt $attempt) => [
                'source' => 'assessment',
                'learner_id' => $learner->id,
                'course_id' => $attempt->assessment?->course_id,
                'assessment_id' => $attempt->assessment_id,
                'attempt_id' => $attempt->id,
                'earned_marks' => $attempt->earned_marks !== null ? (float) $attempt->earned_marks : null,
                'total_marks' => (float) $attempt->total_marks_snapshot,
                'percentage' => $attempt->percentage !== null ? (float) $attempt->percentage : null,
                'passed' => $attempt->passed,
                'completed_at' => $attempt->reviewed_at ?? $attempt->submitted_at,
            ]);
    }
}
