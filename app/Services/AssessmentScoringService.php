<?php

namespace App\Services;

use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use Illuminate\Support\Str;

class AssessmentScoringService
{
    public function scoreAttempt(AssessmentAttempt $attempt): AssessmentAttempt
    {
        abort_if($attempt->status === 'in_progress', 409, 'Attempt must be submitted before scoring.');

        $attempt->loadMissing(['answers.question']);

        foreach ($attempt->answers as $answer) {
            $snapshot = $answer->question_snapshot ?? [];
            $type = $snapshot['type'] ?? $answer->question?->type;

            if ($type === 'short_answer') {
                if ($answer->awarded_marks === null) {
                    $answer->update([
                        'requires_review' => true,
                        'is_correct' => null,
                    ]);
                }
                continue;
            }

            [$correct, $awarded] = $this->scoreObjectiveAnswer($answer, $snapshot);

            $answer->update([
                'awarded_marks' => $awarded,
                'is_correct' => $correct,
                'requires_review' => false,
            ]);
        }

        return $this->finalizeAttempt($attempt);
    }

    public function finalizeAttempt(AssessmentAttempt $attempt): AssessmentAttempt
    {
        $attempt->load('answers');

        $pendingReview = $attempt->answers->contains(
            fn (AssessmentAnswer $answer) => $answer->requires_review
        );

        $earned = round((float) $attempt->answers->sum(
            fn (AssessmentAnswer $answer) => (float) ($answer->awarded_marks ?? 0)
        ), 2);
        $total = (float) $attempt->total_marks_snapshot;
        $percentage = $total > 0 ? round(($earned / $total) * 100, 2) : 0.0;
        $passing = $attempt->passing_marks_snapshot;
        $passed = $passing === null ? null : $earned >= (float) $passing;

        $attempt->update([
            'status' => $pendingReview ? 'pending_review' : 'completed',
            'earned_marks' => $earned,
            'percentage' => $percentage,
            'passed' => $passed,
            'reviewed_at' => $pendingReview ? null : ($attempt->reviewed_at ?? now()),
        ]);

        return $attempt->fresh(['assessment.course.subject', 'learner', 'answers.question']);
    }

    private function scoreObjectiveAnswer(AssessmentAnswer $answer, array $snapshot): array
    {
        $type = $snapshot['type'] ?? $answer->question?->type;
        $answerKey = $snapshot['answer_key'] ?? $answer->question?->answer_key ?? [];
        $marks = (float) ($snapshot['marks'] ?? $answer->question?->marks ?? 0);
        $response = $answer->response ?? [];

        $correct = match ($type) {
            'single_choice' => $this->stringValue($response['value'] ?? null) === $this->stringValue($answerKey['value'] ?? null),
            'multi_select' => $this->normalizedSet($response['values'] ?? []) === $this->normalizedSet($answerKey['values'] ?? []),
            'true_false' => $this->booleanValue($response['value'] ?? null) === $this->booleanValue($answerKey['value'] ?? null),
            'fill_blank' => $this->fillBlankCorrect($response['value'] ?? null, $answerKey['accepted'] ?? []),
            'matching' => $this->matchingCorrect($response['pairs'] ?? [], $answerKey['pairs'] ?? []),
            default => false,
        };

        return [$correct, $correct ? $marks : 0.0];
    }

    private function normalizedSet(array $values): array
    {
        $values = array_values(array_unique(array_map(
            fn ($value) => $this->stringValue($value),
            array_filter($values, fn ($value) => $value !== null && $value !== '')
        )));
        sort($values, SORT_STRING);

        return $values;
    }

    private function fillBlankCorrect(mixed $value, array $accepted): bool
    {
        $given = $this->normalizedText($value);
        if ($given === '') {
            return false;
        }

        return in_array($given, array_map(fn ($item) => $this->normalizedText($item), $accepted), true);
    }

    private function matchingCorrect(array $responsePairs, array $expectedPairs): bool
    {
        $expected = [];
        foreach ($expectedPairs as $pair) {
            if (is_array($pair) && isset($pair['left'], $pair['right'])) {
                $expected[$this->normalizedText($pair['left'])] = $this->normalizedText($pair['right']);
            }
        }

        $given = [];
        foreach ($responsePairs as $left => $right) {
            if (is_array($right) && isset($right['left'], $right['right'])) {
                $given[$this->normalizedText($right['left'])] = $this->normalizedText($right['right']);
            } else {
                $given[$this->normalizedText($left)] = $this->normalizedText($right);
            }
        }

        ksort($expected);
        ksort($given);

        return $expected !== [] && $given === $expected;
    }

    private function stringValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return trim((string) ($value ?? ''));
    }

    private function booleanValue(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return match (strtolower(trim((string) ($value ?? '')))) {
            'true', '1', 'yes' => true,
            'false', '0', 'no' => false,
            default => null,
        };
    }

    private function normalizedText(mixed $value): string
    {
        return Str::of((string) ($value ?? ''))
            ->lower()
            ->squish()
            ->value();
    }
}
