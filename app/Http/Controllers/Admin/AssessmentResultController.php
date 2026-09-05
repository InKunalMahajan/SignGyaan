<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentResultController extends Controller
{
    public function index(Request $request): View
    {
        $query = AssessmentAttempt::query()
            ->with('user', 'assessment.practiceResource.lesson.unit.course.subject');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));

            $query->where(function ($builder) use ($search) {
                $builder
                    ->whereHas('user', fn ($userQuery) => $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('assessment.practiceResource', fn ($practiceQuery) => $practiceQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhereHas('lesson', fn ($lessonQuery) => $lessonQuery->where('title', 'like', "%{$search}%")));
            });
        }

        if ($request->filled('assessment')) {
            $query->where('assessment_id', $request->integer('assessment'));
        }

        if ($request->filled('subject')) {
            $subjectId = $request->integer('subject');
            $query->whereHas('assessment.practiceResource.lesson.unit.course', fn ($courseQuery) => $courseQuery->where('subject_id', $subjectId));
        }

        if (in_array($request->input('status'), ['in-progress', 'submitted', 'expired'], true)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('outcome') === 'passed') {
            $query->where('status', 'submitted')->where('passed', true);
        } elseif ($request->input('outcome') === 'not-passed') {
            $query->where('status', 'submitted')->where('passed', false);
        }

        $allAttempts = AssessmentAttempt::query();
        $submitted = (clone $allAttempts)->where('status', 'submitted');
        $submittedCount = (clone $submitted)->count();
        $passedCount = (clone $submitted)->where('passed', true)->count();

        return view('admin.assessment-results.index', [
            'attempts' => $query
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->paginate(25)
                ->withQueryString(),
            'assessments' => Assessment::query()
                ->with('practiceResource')
                ->orderByDesc('updated_at')
                ->get(),
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'summary' => [
                'total_attempts' => AssessmentAttempt::query()->count(),
                'submitted' => $submittedCount,
                'passed' => $passedCount,
                'pass_rate' => $submittedCount > 0 ? round(($passedCount / $submittedCount) * 100, 1) : null,
                'average_score' => $submittedCount > 0
                    ? round((float) AssessmentAttempt::query()->where('status', 'submitted')->avg('percentage'), 1)
                    : null,
                'in_progress' => AssessmentAttempt::query()->where('status', 'in-progress')->count(),
            ],
        ]);
    }

    public function show(AssessmentAttempt $attempt): View
    {
        $attempt->load([
            'user',
            'assessment.practiceResource.lesson.unit.course.subject',
            'answers.question.options',
        ]);

        $assessment = $attempt->assessment;
        $answers = $attempt->answers->keyBy('assessment_question_id');
        $questions = $assessment->questions()
            ->with('options')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(fn ($question) => $answers->has($question->id))
            ->values();

        return view('admin.assessment-results.show', [
            'attempt' => $attempt,
            'assessment' => $assessment,
            'answers' => $answers,
            'questions' => $questions,
            'correctCount' => $attempt->answers->where('is_correct', true)->count(),
            'answeredCount' => $attempt->answers->filter(fn (AssessmentAnswer $answer) => $answer->response !== null)->count(),
            'questionCount' => $attempt->answers->count(),
        ]);
    }
}
