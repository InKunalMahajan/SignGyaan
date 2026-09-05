<?php

namespace App\Services;

use App\Models\AssessmentQuestion;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AssessmentScoringService
{
    /**
     * Normalise all submitted responses and enforce required questions when requested.
     *
     * @return array<int, array|null>
     */
    public function normaliseResponses(Collection $questions, mixed $rawAnswers, bool $requireRequiredQuestions = false): array
    {
        if (! is_array($rawAnswers)) {
            throw ValidationException::withMessages([
                'answers' => 'The submitted answers are not valid.',
            ]);
        }

        $normalised = [];
        $errors = [];

        foreach ($questions->values() as $index => $question) {
            $response = $this->normaliseResponse($question, $rawAnswers[$question->id] ?? null);
            $normalised[$question->id] = $response;

            if ($requireRequiredQuestions && $question->is_required && $response === null) {
                $errors['answers.'.$question->id] = 'Question '.($index + 1).' is required. Add an answer before submitting.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $normalised;
    }

    public function normaliseResponse(AssessmentQuestion $question, mixed $rawAnswer): ?array
    {
        if (! is_array($rawAnswer)) {
            return null;
        }

        if ($question->question_type === 'fill-blank') {
            $text = trim((string) ($rawAnswer['text'] ?? ''));

            if ($text === '') {
                return null;
            }

            if (mb_strlen($text) > 5000) {
                throw ValidationException::withMessages([
                    'answers.'.$question->id.'.text' => 'This answer is too long.',
                ]);
            }

            return ['text' => $text];
        }

        if (! in_array($question->question_type, ['single-choice', 'multiple-choice', 'true-false'], true)) {
            throw ValidationException::withMessages([
                'answers.'.$question->id => 'This question type cannot be answered by the learner assessment player.',
            ]);
        }

        $optionIds = collect($rawAnswer['option_ids'] ?? [])
            ->map(fn ($id) => filter_var($id, FILTER_VALIDATE_INT))
            ->filter(fn ($id) => $id !== false && $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($optionIds->isEmpty()) {
            return null;
        }

        if (in_array($question->question_type, ['single-choice', 'true-false'], true) && $optionIds->count() !== 1) {
            throw ValidationException::withMessages([
                'answers.'.$question->id => 'Choose exactly one answer for this question.',
            ]);
        }

        $validOptionIds = $question->options
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($optionIds->diff($validOptionIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'answers.'.$question->id => 'One or more selected answers are not valid for this question.',
            ]);
        }

        return ['option_ids' => $optionIds->all()];
    }

    /**
     * @return array{is_correct: bool, points_awarded: float}
     */
    public function grade(AssessmentQuestion $question, ?array $response): array
    {
        if ($response === null) {
            return [
                'is_correct' => false,
                'points_awarded' => 0.0,
            ];
        }

        $isCorrect = match ($question->question_type) {
            'single-choice', 'multiple-choice', 'true-false' => $this->choiceResponseIsCorrect($question, $response),
            'fill-blank' => $this->fillBlankResponseIsCorrect($question, $response),
            default => false,
        };

        return [
            'is_correct' => $isCorrect,
            'points_awarded' => $isCorrect ? (float) $question->points : 0.0,
        ];
    }

    private function choiceResponseIsCorrect(AssessmentQuestion $question, array $response): bool
    {
        $selectedIds = collect($response['option_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values();

        $correctIds = $question->options
            ->where('is_correct', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values();

        return $correctIds->isNotEmpty() && $selectedIds->all() === $correctIds->all();
    }

    private function fillBlankResponseIsCorrect(AssessmentQuestion $question, array $response): bool
    {
        $submitted = $this->normaliseText((string) ($response['text'] ?? ''));

        if ($submitted === '') {
            return false;
        }

        $acceptedAnswers = collect(data_get($question->answer_key, 'accepted_answers', []))
            ->map(fn ($answer) => $this->normaliseText((string) $answer))
            ->filter()
            ->unique()
            ->values();

        return $acceptedAnswers->contains($submitted);
    }

    private function normaliseText(string $value): string
    {
        $value = trim($value);
        $value = (string) preg_replace('/\s+/u', ' ', $value);

        return mb_strtolower($value);
    }
}
