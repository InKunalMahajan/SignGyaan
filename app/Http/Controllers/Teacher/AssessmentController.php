<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $assessments = Assessment::query()
            ->where('created_by', $request->user()->id)
            ->whereHas('course', fn ($query) => $query->where('created_by', $request->user()->id))
            ->with('course.subject')
            ->withCount(['questions', 'attempts'])
            ->latest('id')
            ->paginate(15);

        return view('teacher.assessments.index', [
            'assessments' => $assessments,
        ]);
    }

    public function create(Request $request): View
    {
        return view('teacher.assessments.create', [
            'courses' => $this->ownedCourses($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->assessmentRules());
        $course = $this->ownedCourse($request, (int) $validated['course_id']);

        $assessment = Assessment::create([
            ...$validated,
            'course_id' => $course->id,
            'created_by' => $request->user()->id,
            'status' => 'draft',
            'published_at' => null,
        ]);

        return redirect()
            ->route('teacher.assessments.edit', $assessment)
            ->with('status', 'Assessment draft created. Add questions, preview, then publish.');
    }

    public function edit(Request $request, Assessment $assessment): View
    {
        $this->authorizeAssessment($request, $assessment);
        $assessment->load(['course.subject', 'questions']);

        return view('teacher.assessments.edit', [
            'assessment' => $assessment,
            'courses' => $this->ownedCourses($request),
            'isLocked' => $assessment->attempts()->exists(),
            'questionTypes' => $this->questionTypeLabels(),
        ]);
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorizeAssessment($request, $assessment);
        $this->assertMutable($assessment);

        $validated = $request->validate($this->assessmentRules());
        $course = $this->ownedCourse($request, (int) $validated['course_id']);

        $assessment->update([
            ...$validated,
            'course_id' => $course->id,
        ]);

        return back()->with('status', 'Assessment details updated.');
    }

    public function preview(Request $request, Assessment $assessment): View
    {
        $this->authorizeAssessment($request, $assessment);
        $assessment->load(['course.subject', 'questions']);

        return view('teacher.assessments.preview', [
            'assessment' => $assessment,
            'questionTypes' => $this->questionTypeLabels(),
            'questionMarks' => (float) $assessment->questions->sum(fn ($question) => (float) $question->marks),
        ]);
    }

    public function publish(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorizeAssessment($request, $assessment);
        $this->assertMutable($assessment);
        $this->validateForPublish($assessment);

        $assessment->update([
            'status' => 'published',
            'published_at' => $assessment->published_at ?? now(),
        ]);

        return back()->with('status', 'Assessment published successfully.');
    }

    public function archive(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorizeAssessment($request, $assessment);

        $assessment->update(['status' => 'archived']);

        return back()->with('status', 'Assessment archived. Learners will not see it.');
    }

    public function storeQuestion(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorizeAssessment($request, $assessment);
        $this->assertMutable($assessment);

        $payload = $this->validatedQuestionPayload($request);
        $payload['position'] = $payload['position']
            ?? ((int) $assessment->questions()->max('position') + 1);

        $assessment->questions()->create($payload);

        return back()->with('status', 'Question added.');
    }

    public function updateQuestion(
        Request $request,
        Assessment $assessment,
        AssessmentQuestion $question
    ): RedirectResponse {
        $this->authorizeAssessment($request, $assessment);
        $this->assertQuestionBelongs($assessment, $question);
        $this->assertMutable($assessment);

        $question->update($this->validatedQuestionPayload($request, true));

        return back()->with('status', 'Question updated.');
    }

    public function destroyQuestion(
        Request $request,
        Assessment $assessment,
        AssessmentQuestion $question
    ): RedirectResponse {
        $this->authorizeAssessment($request, $assessment);
        $this->assertQuestionBelongs($assessment, $question);
        $this->assertMutable($assessment);

        $question->delete();
        $this->normalizePositions($assessment);

        return back()->with('status', 'Question removed.');
    }

    public function moveQuestion(
        Request $request,
        Assessment $assessment,
        AssessmentQuestion $question
    ): RedirectResponse {
        $this->authorizeAssessment($request, $assessment);
        $this->assertQuestionBelongs($assessment, $question);
        $this->assertMutable($assessment);

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        $questions = $assessment->questions()->orderBy('position')->orderBy('id')->get()->values();
        $index = $questions->search(fn ($item) => $item->id === $question->id);
        $swapIndex = $validated['direction'] === 'up' ? $index - 1 : $index + 1;

        if ($index !== false && isset($questions[$swapIndex])) {
            $other = $questions[$swapIndex];
            $currentPosition = $question->position;
            $question->update(['position' => $other->position]);
            $other->update(['position' => $currentPosition]);
            $this->normalizePositions($assessment);
        }

        return back()->with('status', 'Question order updated.');
    }

    private function assessmentRules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:180'],
            'instructions' => ['nullable', 'string', 'max:10000'],
            'total_marks' => ['required', 'numeric', 'min:0.5', 'max:10000'],
            'passing_marks' => ['nullable', 'numeric', 'min:0', 'lte:total_marks'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ];
    }

    private function validatedQuestionPayload(Request $request, bool $positionRequired = false): array
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(AssessmentQuestion::TYPES)],
            'prompt' => ['required', 'string', 'max:10000'],
            'marks' => ['required', 'numeric', 'min:0.5', 'max:1000'],
            'position' => [$positionRequired ? 'required' : 'nullable', 'integer', 'min:1', 'max:9999'],
            'explanation' => ['nullable', 'string', 'max:10000'],
            'options' => ['nullable', 'string', 'max:20000'],
            'correct_answers' => ['nullable', 'string', 'max:10000'],
            'matching_pairs' => ['nullable', 'string', 'max:20000'],
        ]);

        [$config, $answerKey] = $this->buildQuestionConfiguration(
            $validated['type'],
            $validated['options'] ?? null,
            $validated['correct_answers'] ?? null,
            $validated['matching_pairs'] ?? null,
        );

        return [
            'type' => $validated['type'],
            'prompt' => trim($validated['prompt']),
            'marks' => $validated['marks'],
            'position' => $validated['position'] ?? null,
            'explanation' => $validated['explanation'] ?? null,
            'config' => $config,
            'answer_key' => $answerKey,
        ];
    }

    private function buildQuestionConfiguration(
        string $type,
        ?string $optionsText,
        ?string $answersText,
        ?string $pairsText
    ): array {
        $options = $this->lines($optionsText);
        $answers = $this->lines($answersText);

        return match ($type) {
            'single_choice' => $this->singleChoiceConfiguration($options, $answers),
            'multi_select' => $this->multiSelectConfiguration($options, $answers),
            'true_false' => $this->trueFalseConfiguration($answers),
            'fill_blank' => $this->fillBlankConfiguration($answers),
            'matching' => $this->matchingConfiguration($pairsText),
            'short_answer' => [[], null],
            default => throw ValidationException::withMessages(['type' => 'Unsupported question type.']),
        };
    }

    private function singleChoiceConfiguration(array $options, array $answers): array
    {
        if (count($options) < 2) {
            throw ValidationException::withMessages(['options' => 'Single-choice questions need at least two options.']);
        }

        if (count($answers) !== 1 || ! in_array($answers[0], $options, true)) {
            throw ValidationException::withMessages(['correct_answers' => 'Enter exactly one correct answer that matches an option.']);
        }

        return [['options' => $options], ['value' => $answers[0]]];
    }

    private function multiSelectConfiguration(array $options, array $answers): array
    {
        if (count($options) < 2) {
            throw ValidationException::withMessages(['options' => 'Multi-select questions need at least two options.']);
        }

        if ($answers === [] || array_diff($answers, $options) !== []) {
            throw ValidationException::withMessages(['correct_answers' => 'Every correct answer must exactly match one of the options.']);
        }

        sort($answers);

        return [['options' => $options], ['values' => array_values($answers)]];
    }

    private function trueFalseConfiguration(array $answers): array
    {
        if (count($answers) !== 1 || ! in_array(strtolower($answers[0]), ['true', 'false'], true)) {
            throw ValidationException::withMessages(['correct_answers' => 'Enter True or False as the correct answer.']);
        }

        return [[], ['value' => strtolower($answers[0]) === 'true']];
    }

    private function fillBlankConfiguration(array $answers): array
    {
        if ($answers === []) {
            throw ValidationException::withMessages(['correct_answers' => 'Add at least one accepted answer for a fill-in-the-blank question.']);
        }

        return [[], ['accepted' => $answers]];
    }

    private function matchingConfiguration(?string $pairsText): array
    {
        $pairs = [];

        foreach ($this->lines($pairsText) as $line) {
            $parts = array_map('trim', explode('=>', $line, 2));
            if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                throw ValidationException::withMessages(['matching_pairs' => 'Use one pair per line in the format Left => Right.']);
            }
            $pairs[] = ['left' => $parts[0], 'right' => $parts[1]];
        }

        if (count($pairs) < 2) {
            throw ValidationException::withMessages(['matching_pairs' => 'Matching questions need at least two pairs.']);
        }

        return [['pairs' => $pairs], ['pairs' => $pairs]];
    }

    private function lines(?string $value): array
    {
        if (! $value) {
            return [];
        }

        return collect(preg_split('/\R/u', $value) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function validateForPublish(Assessment $assessment): void
    {
        $assessment->load('questions');

        if ($assessment->questions->isEmpty()) {
            throw ValidationException::withMessages(['publish' => 'Add at least one question before publishing.']);
        }

        $questionMarks = round((float) $assessment->questions->sum(fn ($question) => (float) $question->marks), 2);
        $assessmentMarks = round((float) $assessment->total_marks, 2);

        if ($questionMarks !== $assessmentMarks) {
            throw ValidationException::withMessages([
                'publish' => "Question marks total {$questionMarks}, but assessment total marks is {$assessmentMarks}. Make them equal before publishing.",
            ]);
        }

        if ($assessment->passing_marks !== null && (float) $assessment->passing_marks > $assessmentMarks) {
            throw ValidationException::withMessages(['publish' => 'Passing marks cannot be greater than total marks.']);
        }
    }

    private function authorizeAssessment(Request $request, Assessment $assessment): void
    {
        $assessment->loadMissing('course');

        abort_unless(
            $assessment->created_by === $request->user()->id
                && $assessment->course?->created_by === $request->user()->id,
            403
        );
    }

    private function assertMutable(Assessment $assessment): void
    {
        if ($assessment->attempts()->exists()) {
            throw ValidationException::withMessages([
                'assessment' => 'This assessment is locked because learner attempts already exist. Archive it and create a new assessment version instead.',
            ]);
        }
    }

    private function assertQuestionBelongs(Assessment $assessment, AssessmentQuestion $question): void
    {
        abort_unless($question->assessment_id === $assessment->id, 404);
    }

    private function ownedCourse(Request $request, int $courseId): Course
    {
        return Course::query()
            ->whereKey($courseId)
            ->where('created_by', $request->user()->id)
            ->firstOrFail();
    }

    private function ownedCourses(Request $request): Collection
    {
        return Course::query()
            ->where('created_by', $request->user()->id)
            ->with('subject')
            ->orderBy('title')
            ->get();
    }

    private function normalizePositions(Assessment $assessment): void
    {
        $assessment->questions()
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(fn ($question, $index) => $question->update(['position' => $index + 1]));
    }

    private function questionTypeLabels(): array
    {
        return [
            'single_choice' => 'MCQ — Single Choice',
            'multi_select' => 'MCQ — Multi-select',
            'true_false' => 'True / False',
            'fill_blank' => 'Fill in the Blank',
            'matching' => 'Match the Following',
            'short_answer' => 'Short Answer',
        ];
    }
}
