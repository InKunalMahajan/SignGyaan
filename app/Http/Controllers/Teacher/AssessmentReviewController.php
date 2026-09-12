<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Services\AssessmentScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssessmentReviewController extends Controller
{
    public function index(Request $request, Assessment $assessment): View
    {
        $this->authorizeAssessment($request, $assessment);

        $attempts = $assessment->attempts()
            ->whereIn('status', ['submitted', 'pending_review', 'completed'])
            ->with('learner')
            ->latest('submitted_at')
            ->paginate(20);

        return view('teacher.assessments.attempts.index', compact('assessment', 'attempts'));
    }

    public function show(
        Request $request,
        AssessmentAttempt $attempt,
        AssessmentScoringService $scoring
    ): View {
        $this->authorizeAttempt($request, $attempt);
        abort_unless($attempt->isSubmitted(), 404);

        $attempt = $scoring->scoreAttempt($attempt);

        return view('teacher.assessments.attempts.review', [
            'attempt' => $attempt,
            'assessment' => $attempt->assessment,
            'answers' => $attempt->answers->sortBy(fn ($answer) => $answer->question_snapshot['position'] ?? $answer->question?->position ?? 0),
        ]);
    }

    public function update(
        Request $request,
        AssessmentAttempt $attempt,
        AssessmentScoringService $scoring
    ): RedirectResponse {
        $this->authorizeAttempt($request, $attempt);
        abort_unless($attempt->isSubmitted(), 404);

        $attempt->load('answers.question');
        $validated = $request->validate([
            'scores' => ['required', 'array'],
            'scores.*' => ['nullable', 'numeric', 'min:0'],
            'feedback' => ['nullable', 'array'],
            'feedback.*' => ['nullable', 'string', 'max:5000'],
        ]);
        $feedbackByAnswer = $validated['feedback'] ?? [];

        foreach ($attempt->answers as $answer) {
            $snapshot = $answer->question_snapshot ?? [];
            $type = $snapshot['type'] ?? $answer->question?->type;
            if ($type !== 'short_answer') {
                continue;
            }

            if (! array_key_exists((string) $answer->id, $validated['scores'])
                && ! array_key_exists($answer->id, $validated['scores'])) {
                continue;
            }

            $rawScore = $validated['scores'][(string) $answer->id] ?? $validated['scores'][$answer->id] ?? null;
            if ($rawScore === null || $rawScore === '') {
                continue;
            }

            $maxMarks = (float) ($snapshot['marks'] ?? $answer->question?->marks ?? 0);
            $score = round((float) $rawScore, 2);

            if ($score > $maxMarks) {
                throw ValidationException::withMessages([
                    "scores.{$answer->id}" => "Score cannot exceed {$maxMarks} marks.",
                ]);
            }

            $feedback = $feedbackByAnswer[(string) $answer->id]
                ?? $feedbackByAnswer[$answer->id]
                ?? null;

            $answer->update([
                'awarded_marks' => $score,
                'is_correct' => $maxMarks > 0 ? $score >= $maxMarks : null,
                'requires_review' => false,
                'feedback' => $feedback,
            ]);
        }

        $scoring->scoreAttempt($attempt->fresh());

        return back()->with('status', 'Manual marks saved and result recalculated.');
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

    private function authorizeAttempt(Request $request, AssessmentAttempt $attempt): void
    {
        $attempt->loadMissing('assessment.course');
        $this->authorizeAssessment($request, $attempt->assessment);
    }
}
