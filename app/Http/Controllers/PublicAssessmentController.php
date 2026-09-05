<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Services\AssessmentScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicAssessmentController extends Controller
{
    public function show(Request $request, int $assessment): View
    {
        $assessmentModel = $this->publishedAssessment($assessment);
        $publishedQuestionsCount = $assessmentModel->questions()->published()->count();

        $activeAttempt = null;
        $attemptsUsed = 0;

        if ($request->user()) {
            $this->expireTimedOutAttempts($assessmentModel, $request->user()->id);

            $attemptsUsed = $assessmentModel->attempts()
                ->where('user_id', $request->user()->id)
                ->count();

            $activeAttempt = $assessmentModel->attempts()
                ->where('user_id', $request->user()->id)
                ->where('status', 'in-progress')
                ->latest('id')
                ->first();
        }

        return view('pages.assessments.show', [
            'assessment' => $assessmentModel,
            'publishedQuestionsCount' => $publishedQuestionsCount,
            'activeAttempt' => $activeAttempt,
            'attemptsUsed' => $attemptsUsed,
            'attemptsRemaining' => $assessmentModel->max_attempts === null
                ? null
                : max(0, $assessmentModel->max_attempts - $attemptsUsed),
        ]);
    }

    public function start(Request $request, int $assessment): RedirectResponse
    {
        $assessmentModel = $this->publishedAssessment($assessment);
        $user = $request->user();

        $this->expireTimedOutAttempts($assessmentModel, $user->id);

        $activeAttempt = $assessmentModel->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'in-progress')
            ->latest('id')
            ->first();

        if ($activeAttempt) {
            return redirect()->route('assessment-attempts.show', [$assessmentModel, $activeAttempt]);
        }

        $publishedQuestionsCount = $assessmentModel->questions()->published()->count();

        if ($publishedQuestionsCount === 0) {
            return back()->withErrors([
                'assessment' => 'This assessment does not have any published questions yet.',
            ]);
        }

        $attemptsUsed = $assessmentModel->attempts()
            ->where('user_id', $user->id)
            ->count();

        if ($assessmentModel->max_attempts !== null && $attemptsUsed >= $assessmentModel->max_attempts) {
            return back()->withErrors([
                'assessment' => 'You have used all available attempts for this assessment.',
            ]);
        }

        $attemptNumber = ((int) $assessmentModel->attempts()
            ->where('user_id', $user->id)
            ->max('attempt_number')) + 1;

        $startedAt = now();

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessmentModel->id,
            'user_id' => $user->id,
            'attempt_number' => $attemptNumber,
            'status' => 'in-progress',
            'started_at' => $startedAt,
            'expires_at' => $assessmentModel->time_limit_minutes
                ? $startedAt->copy()->addMinutes($assessmentModel->time_limit_minutes)
                : null,
        ]);

        return redirect()
            ->route('assessment-attempts.show', [$assessmentModel, $attempt])
            ->with('status', 'Assessment started. Your attempt is now in progress.');
    }

    public function play(Request $request, int $assessment, AssessmentAttempt $attempt): View|RedirectResponse
    {
        $assessmentModel = $this->publishedAssessment($assessment);
        $this->authorizeAttempt($assessmentModel, $attempt, $request->user()->id);

        if ($attempt->status === 'in-progress' && $attempt->expires_at && $attempt->expires_at->isPast()) {
            $attempt->update(['status' => 'expired']);

            return redirect()
                ->route('assessments.show', $assessmentModel)
                ->withErrors(['assessment' => 'This timed assessment attempt has expired.']);
        }

        abort_unless($attempt->status === 'in-progress', 404);

        $questions = $assessmentModel->questions()
            ->published()
            ->with('options')
            ->get();

        abort_if($questions->isEmpty(), 404);

        $questions = $this->orderedQuestionsForAttempt($assessmentModel, $attempt, $questions);

        $savedAnswers = $attempt->answers()
            ->whereIn('assessment_question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('assessment_question_id');

        $practice = $assessmentModel->practiceResource;
        $lesson = $practice->lesson;
        $course = $lesson->unit->course;
        $subject = $course->subject;

        return view('pages.assessments.player', [
            'assessment' => $assessmentModel,
            'attempt' => $attempt,
            'questions' => $questions,
            'savedAnswers' => $savedAnswers,
            'lessonUrl' => route('courses.show', [
                'subject' => $subject->slug,
                'course' => $course->slug,
                'lesson' => 'lesson-'.$lesson->id,
            ]),
        ]);
    }

    public function save(
        Request $request,
        int $assessment,
        AssessmentAttempt $attempt,
        AssessmentScoringService $scoring
    ): RedirectResponse {
        $assessmentModel = $this->publishedAssessment($assessment);
        $this->authorizeAttempt($assessmentModel, $attempt, $request->user()->id);

        if ($attempt->expires_at && $attempt->expires_at->isPast()) {
            $attempt->update(['status' => 'expired']);

            return redirect()
                ->route('assessments.show', $assessmentModel)
                ->withErrors(['assessment' => 'This timed assessment attempt has expired.']);
        }

        abort_unless($attempt->status === 'in-progress', 404);

        $questions = $assessmentModel->questions()
            ->published()
            ->with('options')
            ->get();

        $responses = $scoring->normaliseResponses(
            $questions,
            $request->input('answers', []),
            false
        );

        DB::transaction(function () use ($attempt, $questions, $responses): void {
            foreach ($questions as $question) {
                $response = $responses[$question->id] ?? null;

                if ($response === null) {
                    $attempt->answers()
                        ->where('assessment_question_id', $question->id)
                        ->delete();
                    continue;
                }

                AssessmentAnswer::updateOrCreate(
                    [
                        'assessment_attempt_id' => $attempt->id,
                        'assessment_question_id' => $question->id,
                    ],
                    [
                        'response' => $response,
                        'question_snapshot' => $question->prompt,
                        'is_correct' => null,
                        'points_awarded' => 0,
                        'answered_at' => now(),
                    ]
                );
            }
        });

        return redirect()
            ->route('assessment-attempts.show', [$assessmentModel, $attempt])
            ->with('status', 'Your answers have been saved. You can continue editing them before submission.');
    }

    public function submit(
        Request $request,
        int $assessment,
        AssessmentAttempt $attempt,
        AssessmentScoringService $scoring
    ): RedirectResponse {
        $assessmentModel = $this->publishedAssessment($assessment);
        $this->authorizeAttempt($assessmentModel, $attempt, $request->user()->id);

        if ($attempt->status !== 'in-progress') {
            return redirect()
                ->route('assessments.show', $assessmentModel)
                ->withErrors(['assessment' => 'This assessment attempt is no longer open for submission.']);
        }

        if ($attempt->expires_at && $attempt->expires_at->isPast()) {
            $attempt->update(['status' => 'expired']);

            return redirect()
                ->route('assessments.show', $assessmentModel)
                ->withErrors(['assessment' => 'This timed assessment attempt expired before it could be submitted.']);
        }

        $questions = $assessmentModel->questions()
            ->published()
            ->with('options')
            ->get();

        if ($questions->isEmpty()) {
            throw ValidationException::withMessages([
                'assessment' => 'This assessment does not have any published questions to score.',
            ]);
        }

        $responses = $scoring->normaliseResponses(
            $questions,
            $request->input('answers', []),
            true
        );

        $result = DB::transaction(function () use ($assessmentModel, $attempt, $questions, $responses, $scoring): array {
            $lockedAttempt = AssessmentAttempt::query()
                ->whereKey($attempt->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedAttempt->status !== 'in-progress') {
                return ['state' => 'closed'];
            }

            if ($lockedAttempt->expires_at && $lockedAttempt->expires_at->isPast()) {
                $lockedAttempt->update(['status' => 'expired']);

                return ['state' => 'expired'];
            }

            $scorePoints = 0.0;
            $maxPoints = $questions->sum(fn (AssessmentQuestion $question) => (float) $question->points);

            foreach ($questions as $question) {
                $response = $responses[$question->id] ?? null;

                if ($response === null) {
                    $lockedAttempt->answers()
                        ->where('assessment_question_id', $question->id)
                        ->delete();
                    continue;
                }

                $grade = $scoring->grade($question, $response);
                $scorePoints += $grade['points_awarded'];

                AssessmentAnswer::updateOrCreate(
                    [
                        'assessment_attempt_id' => $lockedAttempt->id,
                        'assessment_question_id' => $question->id,
                    ],
                    [
                        'response' => $response,
                        'question_snapshot' => $question->prompt,
                        'is_correct' => $grade['is_correct'],
                        'points_awarded' => $grade['points_awarded'],
                        'answered_at' => now(),
                    ]
                );
            }

            $percentage = $maxPoints > 0
                ? round(($scorePoints / $maxPoints) * 100, 2)
                : 0.0;
            $passed = $percentage >= $assessmentModel->passing_percentage;

            $lockedAttempt->update([
                'status' => 'submitted',
                'score_points' => round($scorePoints, 2),
                'max_points' => round($maxPoints, 2),
                'percentage' => $percentage,
                'passed' => $passed,
                'submitted_at' => now(),
            ]);

            return [
                'state' => 'submitted',
                'percentage' => $percentage,
                'passed' => $passed,
            ];
        });

        if ($result['state'] === 'expired') {
            return redirect()
                ->route('assessments.show', $assessmentModel)
                ->withErrors(['assessment' => 'This timed assessment attempt expired before it could be submitted.']);
        }

        if ($result['state'] === 'closed') {
            return redirect()
                ->route('assessments.show', $assessmentModel)
                ->withErrors(['assessment' => 'This assessment attempt has already been closed.']);
        }

        $outcome = $result['passed'] ? 'Passed' : 'Not passed yet';

        return redirect()
            ->route('assessments.show', $assessmentModel)
            ->with('status', 'Assessment submitted. Score: '.number_format($result['percentage'], 2).'% · '.$outcome.'.');
    }

    private function publishedAssessment(int $assessmentId): Assessment
    {
        return Assessment::query()
            ->published()
            ->whereKey($assessmentId)
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
                            ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published())))))
            ->with('practiceResource.lesson.unit.course.subject')
            ->firstOrFail();
    }

    private function authorizeAttempt(Assessment $assessment, AssessmentAttempt $attempt, int $userId): void
    {
        abort_unless(
            (int) $attempt->assessment_id === (int) $assessment->id
            && (int) $attempt->user_id === $userId,
            404
        );
    }

    private function expireTimedOutAttempts(Assessment $assessment, int $userId): void
    {
        $assessment->attempts()
            ->where('user_id', $userId)
            ->where('status', 'in-progress')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);
    }

    private function orderedQuestionsForAttempt(Assessment $assessment, AssessmentAttempt $attempt, Collection $questions): Collection
    {
        if ($assessment->shuffle_questions) {
            $questions = $questions
                ->sortBy(fn (AssessmentQuestion $question) => hash('sha256', $attempt->id.':question:'.$question->id))
                ->values();
        }

        if ($assessment->shuffle_options) {
            $questions->each(function (AssessmentQuestion $question) use ($attempt): void {
                if ($question->options->isNotEmpty()) {
                    $question->setRelation(
                        'options',
                        $question->options
                            ->sortBy(fn ($option) => hash('sha256', $attempt->id.':option:'.$option->id))
                            ->values()
                    );
                }
            });
        }

        return $questions;
    }
}
