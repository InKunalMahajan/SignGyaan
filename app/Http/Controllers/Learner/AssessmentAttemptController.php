<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Services\AssessmentScoringService;
use App\Services\LearnerLearningPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AssessmentAttemptController extends Controller
{
    public function index(Request $request, LearnerLearningPath $learningPath): View
    {
        $courseIds = $this->accessibleCourseIds($request, $learningPath);

        $assessments = Assessment::query()
            ->whereIn('course_id', $courseIds)
            ->where('status', 'published')
            ->with(['course.subject'])
            ->withCount('questions')
            ->orderByDesc('published_at')
            ->orderBy('title')
            ->get();

        $attempts = AssessmentAttempt::query()
            ->where('learner_id', $request->user()->id)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->orderByDesc('id')
            ->get()
            ->groupBy('assessment_id');

        return view('learner.assessments.index', compact('assessments', 'attempts'));
    }

    public function start(Request $request, Assessment $assessment, LearnerLearningPath $learningPath): RedirectResponse
    {
        $this->assertAssessmentAccessible($request, $assessment, $learningPath);

        $activeAttempt = AssessmentAttempt::query()
            ->where('assessment_id', $assessment->id)
            ->where('learner_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if ($activeAttempt) {
            return redirect()
                ->route('learner.assessments.attempts.show', $activeAttempt)
                ->with('status', 'Your saved attempt has been resumed.');
        }

        $attemptNumber = ((int) AssessmentAttempt::query()
            ->where('assessment_id', $assessment->id)
            ->where('learner_id', $request->user()->id)
            ->max('attempt_number')) + 1;

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $request->user()->id,
            'attempt_number' => $attemptNumber,
            'status' => 'in_progress',
            'total_marks_snapshot' => $assessment->total_marks,
            'passing_marks_snapshot' => $assessment->passing_marks,
            'started_at' => now(),
        ]);

        return redirect()
            ->route('learner.assessments.attempts.show', $attempt)
            ->with('status', 'Assessment started. Your answers can be saved and resumed later.');
    }

    public function show(Request $request, AssessmentAttempt $attempt): View
    {
        $this->assertAttemptOwner($request, $attempt);
        abort_if($attempt->isSubmitted(), 409, 'This assessment has already been submitted.');

        $attempt->load(['assessment.course.subject', 'assessment.questions', 'answers']);

        return view('learner.assessments.attempt', [
            'attempt' => $attempt,
            'assessment' => $attempt->assessment,
            'questions' => $attempt->assessment->questions,
            'answersByQuestion' => $attempt->answers->keyBy('question_id'),
        ]);
    }

    public function save(Request $request, AssessmentAttempt $attempt): RedirectResponse
    {
        $this->assertAttemptOwner($request, $attempt);
        abort_if($attempt->isSubmitted(), 409, 'Submitted attempts cannot be changed.');

        $attempt->load('assessment.questions');
        $validated = $request->validate([
            'responses' => ['nullable', 'array'],
            'responses.*' => ['nullable'],
        ]);

        $responses = collect($validated['responses'] ?? []);

        foreach ($attempt->assessment->questions as $question) {
            if (! $responses->has((string) $question->id) && ! $responses->has($question->id)) {
                continue;
            }

            $raw = $responses->get((string) $question->id, $responses->get($question->id));
            $response = $this->normalizeResponse($question, $raw);

            AssessmentAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                ['response' => $response, 'requires_review' => $question->requiresManualReview()]
            );
        }

        return back()->with('status', 'Answers saved. You can continue now or return later.');
    }

    public function review(Request $request, AssessmentAttempt $attempt): View
    {
        $this->assertAttemptOwner($request, $attempt);
        abort_if($attempt->isSubmitted(), 409, 'This assessment has already been submitted.');

        $attempt->load(['assessment.course.subject', 'assessment.questions', 'answers']);

        return view('learner.assessments.review', [
            'attempt' => $attempt,
            'assessment' => $attempt->assessment,
            'questions' => $attempt->assessment->questions,
            'answersByQuestion' => $attempt->answers->keyBy('question_id'),
        ]);
    }

    public function submit(
        Request $request,
        AssessmentAttempt $attempt,
        AssessmentScoringService $scoring
    ): RedirectResponse {
        $this->assertAttemptOwner($request, $attempt);
        abort_if($attempt->isSubmitted(), 409, 'This assessment has already been submitted.');

        $attempt->load(['assessment.questions', 'answers']);
        $answers = $attempt->answers->keyBy('question_id');
        $hasManualReview = false;

        foreach ($attempt->assessment->questions as $question) {
            $answer = $answers->get($question->id);
            $hasManualReview = $hasManualReview || $question->requiresManualReview();

            if (! $answer) {
                $answer = AssessmentAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'response' => null,
                    'requires_review' => $question->requiresManualReview(),
                ]);
            }

            $answer->update([
                'question_snapshot' => [
                    'type' => $question->type,
                    'prompt' => $question->prompt,
                    'marks' => (string) $question->marks,
                    'position' => $question->position,
                    'config' => $question->config,
                    'answer_key' => $question->answer_key,
                    'explanation' => $question->explanation,
                ],
                'requires_review' => $question->requiresManualReview(),
            ]);
        }

        $attempt->update([
            'status' => $hasManualReview ? 'pending_review' : 'submitted',
            'submitted_at' => now(),
        ]);

        $scoring->scoreAttempt($attempt->fresh());

        return redirect()
            ->route('learner.assessments.attempts.result', $attempt)
            ->with('status', $hasManualReview
                ? 'Assessment submitted. Objective questions were scored. Short answers are waiting for teacher review.'
                : 'Assessment submitted and scored successfully.');
    }

    public function result(
        Request $request,
        AssessmentAttempt $attempt,
        AssessmentScoringService $scoring
    ): View {
        $this->assertAttemptOwner($request, $attempt);
        abort_unless($attempt->isSubmitted(), 404);

        $attempt = $scoring->scoreAttempt($attempt);

        return view('learner.assessments.result', [
            'attempt' => $attempt,
            'assessment' => $attempt->assessment,
            'answers' => $attempt->answers->sortBy(fn ($answer) => $answer->question_snapshot['position'] ?? $answer->question?->position ?? 0),
        ]);
    }

    private function assertAssessmentAccessible(Request $request, Assessment $assessment, LearnerLearningPath $learningPath): void
    {
        abort_unless($assessment->status === 'published', 404);
        abort_unless($this->accessibleCourseIds($request, $learningPath)->contains($assessment->course_id), 403);
    }

    private function accessibleCourseIds(Request $request, LearnerLearningPath $learningPath): Collection
    {
        return $learningPath->activeClassesFor($request->user())
            ->flatMap(fn ($class) => $class->courses)
            ->pluck('id')
            ->unique()
            ->values();
    }

    private function assertAttemptOwner(Request $request, AssessmentAttempt $attempt): void
    {
        abort_unless($attempt->learner_id === $request->user()->id, 403);
    }

    private function normalizeResponse(AssessmentQuestion $question, mixed $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        return match ($question->type) {
            'multi_select' => ['values' => array_values(array_filter((array) $raw, fn ($value) => $value !== null && $value !== ''))],
            'matching' => ['pairs' => is_array($raw) ? $raw : []],
            default => ['value' => is_scalar($raw) ? trim((string) $raw) : $raw],
        };
    }
}
