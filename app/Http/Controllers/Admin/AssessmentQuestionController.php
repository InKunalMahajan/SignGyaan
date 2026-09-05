<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssessmentQuestionController extends Controller
{
    public function index(Assessment $assessment): View
    {
        $assessment->load('practiceResource.lesson.unit.course.subject');

        $questions = $assessment->questions()
            ->with('options')
            ->withCount('answers')
            ->get();

        return view('admin.assessment-questions.index', [
            'assessment' => $assessment,
            'questions' => $questions,
            'questionTypes' => $this->questionTypes(),
            'publishedQuestions' => $questions->where('is_published', true)->count(),
            'totalPoints' => $questions->where('is_published', true)->sum(fn ($question) => (float) $question->points),
        ]);
    }

    public function create(Assessment $assessment): View
    {
        $assessment->load('practiceResource.lesson.unit.course.subject');

        return view('admin.assessment-questions.create', [
            'assessment' => $assessment,
            'questionTypes' => $this->questionTypes(),
            'nextSortOrder' => ((int) $assessment->questions()->max('sort_order')) + 1,
        ]);
    }

    public function store(Request $request, Assessment $assessment): RedirectResponse
    {
        $payload = $this->validateQuestion($request);

        DB::transaction(function () use ($assessment, $payload): void {
            $question = $assessment->questions()->create($payload['question']);
            $this->syncAnswerConfiguration($question, $payload);
        });

        return redirect()
            ->route('admin.assessments.questions.index', $assessment)
            ->with('status', 'Assessment question created successfully.');
    }

    public function edit(Assessment $assessment, AssessmentQuestion $question): View
    {
        $this->ensureQuestionBelongsToAssessment($assessment, $question);

        $assessment->load('practiceResource.lesson.unit.course.subject');
        $question->load('options')->loadCount('answers');

        return view('admin.assessment-questions.edit', [
            'assessment' => $assessment,
            'question' => $question,
            'questionTypes' => $this->questionTypes(),
        ]);
    }

    public function update(Request $request, Assessment $assessment, AssessmentQuestion $question): RedirectResponse
    {
        $this->ensureQuestionBelongsToAssessment($assessment, $question);
        $question->load('options')->loadCount('answers');

        $payload = $this->validateQuestion($request);
        $hasLearnerAnswers = $question->answers_count > 0;

        if ($hasLearnerAnswers && $this->scoringSignatureFromQuestion($question) !== $this->scoringSignatureFromPayload($payload)) {
            throw ValidationException::withMessages([
                'question_type' => 'This question already has learner answers. Its type, points or answer configuration cannot be changed. You can edit the wording, explanation, order and publishing settings.',
            ]);
        }

        DB::transaction(function () use ($question, $payload, $hasLearnerAnswers): void {
            $question->update($payload['question']);

            if (! $hasLearnerAnswers) {
                $this->syncAnswerConfiguration($question, $payload);
            }
        });

        return redirect()
            ->route('admin.assessments.questions.edit', [$assessment, $question])
            ->with('status', 'Assessment question updated successfully.');
    }

    public function destroy(Assessment $assessment, AssessmentQuestion $question): RedirectResponse
    {
        $this->ensureQuestionBelongsToAssessment($assessment, $question);

        if ($question->answers()->exists()) {
            return back()->withErrors([
                'question' => 'This question has learner answers and cannot be deleted. Unpublish it instead to keep attempt history intact.',
            ]);
        }

        $question->delete();

        return redirect()
            ->route('admin.assessments.questions.index', $assessment)
            ->with('status', 'Assessment question deleted successfully.');
    }

    private function validateQuestion(Request $request): array
    {
        $questionType = (string) $request->input('question_type');
        $allowedTypes = array_keys($this->questionTypes());

        $rules = [
            'question_type' => ['required', Rule::in($allowedTypes)],
            'prompt' => ['required', 'string', 'max:10000'],
            'explanation' => ['nullable', 'string', 'max:50000'],
            'points' => ['required', 'numeric', 'min:0.01', 'max:10000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ];

        if (in_array($questionType, ['single-choice', 'multiple-choice'], true)) {
            $rules['options'] = ['required', 'array', 'min:2', 'max:20'];
            $rules['options.*.option_text'] = ['required', 'string', 'max:5000'];
            $rules['options.*.feedback'] = ['nullable', 'string', 'max:10000'];
            $rules['options.*.is_correct'] = ['nullable', 'boolean'];
        } elseif ($questionType === 'true-false') {
            $rules['true_false_answer'] = ['required', Rule::in(['true', 'false'])];
        } elseif ($questionType === 'fill-blank') {
            $rules['accepted_answers'] = ['required', 'string', 'max:20000'];
        }

        $validated = $request->validate($rules);

        $payload = [
            'question' => [
                'question_type' => $validated['question_type'],
                'prompt' => trim($validated['prompt']),
                'explanation' => filled($validated['explanation'] ?? null) ? trim($validated['explanation']) : null,
                'answer_key' => null,
                'points' => $validated['points'],
                'sort_order' => $validated['sort_order'],
                'is_required' => $request->boolean('is_required'),
                'is_published' => $request->boolean('is_published'),
            ],
            'options' => [],
            'true_false_answer' => null,
            'accepted_answers' => [],
        ];

        if (in_array($questionType, ['single-choice', 'multiple-choice'], true)) {
            $options = collect($validated['options'])
                ->map(fn (array $option, int $index) => [
                    'option_text' => trim($option['option_text']),
                    'feedback' => filled($option['feedback'] ?? null) ? trim($option['feedback']) : null,
                    'is_correct' => filter_var($option['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'sort_order' => $index + 1,
                ])
                ->values();

            $normalisedTexts = $options
                ->pluck('option_text')
                ->map(fn (string $text) => mb_strtolower(trim($text)));

            if ($normalisedTexts->unique()->count() !== $normalisedTexts->count()) {
                throw ValidationException::withMessages([
                    'options' => 'Answer options must be unique within a question.',
                ]);
            }

            $correctCount = $options->where('is_correct', true)->count();

            if ($questionType === 'single-choice' && $correctCount !== 1) {
                throw ValidationException::withMessages([
                    'options' => 'Single Choice questions must have exactly one correct answer.',
                ]);
            }

            if ($questionType === 'multiple-choice' && $correctCount < 1) {
                throw ValidationException::withMessages([
                    'options' => 'Multiple Choice questions must have at least one correct answer.',
                ]);
            }

            $payload['options'] = $options->all();
        }

        if ($questionType === 'true-false') {
            $payload['true_false_answer'] = $validated['true_false_answer'];
        }

        if ($questionType === 'fill-blank') {
            $acceptedAnswers = collect(preg_split('/\R/u', $validated['accepted_answers']) ?: [])
                ->map(fn ($answer) => trim((string) $answer))
                ->filter()
                ->unique(fn (string $answer) => mb_strtolower($answer))
                ->values();

            if ($acceptedAnswers->isEmpty()) {
                throw ValidationException::withMessages([
                    'accepted_answers' => 'Add at least one accepted answer.',
                ]);
            }

            if ($acceptedAnswers->count() > 50) {
                throw ValidationException::withMessages([
                    'accepted_answers' => 'Add no more than 50 accepted answers.',
                ]);
            }

            $payload['accepted_answers'] = $acceptedAnswers->all();
            $payload['question']['answer_key'] = [
                'accepted_answers' => $acceptedAnswers->all(),
            ];
        }

        return $payload;
    }

    private function syncAnswerConfiguration(AssessmentQuestion $question, array $payload): void
    {
        $question->options()->delete();

        if (in_array($question->question_type, ['single-choice', 'multiple-choice'], true)) {
            $question->options()->createMany($payload['options']);
            return;
        }

        if ($question->question_type === 'true-false') {
            $correctAnswer = $payload['true_false_answer'];

            $question->options()->createMany([
                [
                    'option_text' => 'True',
                    'is_correct' => $correctAnswer === 'true',
                    'sort_order' => 1,
                ],
                [
                    'option_text' => 'False',
                    'is_correct' => $correctAnswer === 'false',
                    'sort_order' => 2,
                ],
            ]);
        }
    }

    private function scoringSignatureFromQuestion(AssessmentQuestion $question): string
    {
        $configuration = [
            'question_type' => $question->question_type,
            'points' => number_format((float) $question->points, 2, '.', ''),
            'answer_key' => $question->answer_key,
            'options' => $question->options
                ->map(fn ($option) => [
                    'option_text' => $option->option_text,
                    'feedback' => $option->feedback,
                    'is_correct' => (bool) $option->is_correct,
                    'sort_order' => (int) $option->sort_order,
                ])
                ->values()
                ->all(),
        ];

        return json_encode($configuration, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function scoringSignatureFromPayload(array $payload): string
    {
        $options = [];

        if (in_array($payload['question']['question_type'], ['single-choice', 'multiple-choice'], true)) {
            $options = $payload['options'];
        } elseif ($payload['question']['question_type'] === 'true-false') {
            $options = [
                [
                    'option_text' => 'True',
                    'feedback' => null,
                    'is_correct' => $payload['true_false_answer'] === 'true',
                    'sort_order' => 1,
                ],
                [
                    'option_text' => 'False',
                    'feedback' => null,
                    'is_correct' => $payload['true_false_answer'] === 'false',
                    'sort_order' => 2,
                ],
            ];
        }

        $configuration = [
            'question_type' => $payload['question']['question_type'],
            'points' => number_format((float) $payload['question']['points'], 2, '.', ''),
            'answer_key' => $payload['question']['answer_key'],
            'options' => $options,
        ];

        return json_encode($configuration, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function ensureQuestionBelongsToAssessment(Assessment $assessment, AssessmentQuestion $question): void
    {
        abort_unless((int) $question->assessment_id === (int) $assessment->id, 404);
    }

    private function questionTypes(): array
    {
        return [
            'single-choice' => 'Single Choice',
            'multiple-choice' => 'Multiple Choice',
            'true-false' => 'True / False',
            'fill-blank' => 'Fill in the Blank',
        ];
    }
}
