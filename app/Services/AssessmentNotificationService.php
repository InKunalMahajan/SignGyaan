<?php

namespace App\Services;

use App\Models\AssessmentAttempt;

class AssessmentNotificationService
{
    public function __construct(private InAppNotificationService $notifications)
    {
    }

    public function submitted(AssessmentAttempt $attempt): void
    {
        $attempt->loadMissing('user', 'assessment.practiceResource');

        if (! $attempt->user || ! $attempt->assessment || $attempt->status !== 'submitted') {
            return;
        }

        $assessment = $attempt->assessment;
        $practice = $assessment->practiceResource;
        $title = $practice?->title ?: 'Assessment';
        $percentage = round((float) $attempt->percentage, 2);
        $passed = (bool) $attempt->passed;
        $attemptsUsed = $assessment->attempts()
            ->where('user_id', $attempt->user_id)
            ->count();
        $attemptsRemaining = $assessment->max_attempts === null
            ? null
            : max(0, (int) $assessment->max_attempts - $attemptsUsed);
        $canRetry = ! $passed && ($assessment->max_attempts === null || $attemptsRemaining > 0);

        $this->notifications->sendOnce(
            user: $attempt->user,
            dedupeKey: 'assessment-result:'.$attempt->id,
            category: 'assessment',
            title: $passed ? 'Assessment passed' : 'Assessment result ready',
            message: $passed
                ? sprintf('You scored %s%% in %s and passed this attempt.', $this->formatPercentage($percentage), $title)
                : sprintf('You scored %s%% in %s. Review your result and feedback.', $this->formatPercentage($percentage), $title),
            url: route('assessment-attempts.result', [$assessment, $attempt]),
            actionLabel: 'Review Result',
            meta: [
                'assessment_id' => $assessment->id,
                'attempt_id' => $attempt->id,
                'attempt_number' => $attempt->attempt_number,
                'percentage' => $percentage,
                'passed' => $passed,
                'attempts_remaining' => $attemptsRemaining,
            ],
        );

        if ($canRetry) {
            $retryMessage = $attemptsRemaining === null
                ? sprintf('You can try %s again when you are ready.', $title)
                : sprintf(
                    'You can try %s again. %d %s remaining.',
                    $title,
                    $attemptsRemaining,
                    $attemptsRemaining === 1 ? 'attempt' : 'attempts'
                );

            $this->notifications->sendOnce(
                user: $attempt->user,
                dedupeKey: 'assessment-retry:'.$attempt->id,
                category: 'assessment',
                title: 'Another attempt is available',
                message: $retryMessage,
                url: route('assessments.show', $assessment),
                actionLabel: 'Try Again',
                meta: [
                    'assessment_id' => $assessment->id,
                    'attempt_id' => $attempt->id,
                    'attempt_number' => $attempt->attempt_number,
                    'attempts_remaining' => $attemptsRemaining,
                ],
            );
        }
    }

    private function formatPercentage(float $percentage): string
    {
        return rtrim(rtrim(number_format($percentage, 2, '.', ''), '0'), '.');
    }
}
