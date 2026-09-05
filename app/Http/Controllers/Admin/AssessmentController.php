<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Assessment::query()
            ->with('practiceResource.lesson.unit.course.subject')
            ->withCount(['questions', 'attempts']);

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));

            $query->whereHas('practiceResource', function ($practiceQuery) use ($search) {
                $practiceQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhereHas('lesson', fn ($lessonQuery) => $lessonQuery->where('title', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('subject')) {
            $subjectId = $request->integer('subject');
            $query->whereHas('practiceResource.lesson.unit.course', fn ($courseQuery) => $courseQuery->where('subject_id', $subjectId));
        }

        if ($request->filled('course')) {
            $courseId = $request->integer('course');
            $query->whereHas('practiceResource.lesson.unit', fn ($unitQuery) => $unitQuery->where('course_id', $courseId));
        }

        if ($request->filled('lesson')) {
            $lessonId = $request->integer('lesson');
            $query->whereHas('practiceResource', fn ($practiceQuery) => $practiceQuery->where('lesson_id', $lessonId));
        }

        if (in_array($request->input('type'), ['quiz', 'exercise'], true)) {
            $query->whereHas('practiceResource', fn ($practiceQuery) => $practiceQuery->where('resource_type', $request->input('type')));
        }

        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        return view('admin.assessments.index', [
            'assessments' => $query
                ->orderByDesc('is_published')
                ->orderByDesc('updated_at')
                ->paginate(20)
                ->withQueryString(),
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
            'lessons' => Lesson::query()->with('unit.course.subject')->orderBy('unit_id')->orderBy('sort_order')->orderBy('title')->get(),
            'totalAssessments' => Assessment::query()->count(),
            'publishedAssessments' => Assessment::query()->where('is_published', true)->count(),
            'draftAssessments' => Assessment::query()->where('is_published', false)->count(),
            'totalAttempts' => Assessment::query()->withCount('attempts')->get()->sum('attempts_count'),
        ]);
    }

    public function create(Request $request): View
    {
        $selectedPracticeId = $request->integer('practice') ?: null;

        return view('admin.assessments.create', [
            'practiceResources' => $this->eligiblePracticeResources(),
            'selectedPracticeId' => $selectedPracticeId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAssessment($request);
        $validated = $this->withBooleanSettings($request, $validated);

        $assessment = Assessment::create($validated);

        return redirect()
            ->route('admin.assessments.edit', $assessment)
            ->with('status', 'Assessment created. Configure its settings now; question building is available in Step 9C.');
    }

    public function edit(Assessment $assessment): View
    {
        $assessment->load('practiceResource.lesson.unit.course.subject')
            ->loadCount(['questions', 'attempts']);

        return view('admin.assessments.edit', [
            'assessment' => $assessment,
            'practiceResources' => $this->eligiblePracticeResources($assessment),
        ]);
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $this->validateAssessment($request, $assessment);
        $validated = $this->withBooleanSettings($request, $validated);

        $assessment->update($validated);

        return redirect()
            ->route('admin.assessments.edit', $assessment)
            ->with('status', 'Assessment settings updated successfully.');
    }

    public function destroy(Assessment $assessment): RedirectResponse
    {
        if ($assessment->attempts()->exists()) {
            return back()->withErrors([
                'assessment' => 'This assessment has learner attempts and cannot be deleted. Keep it as a draft instead.',
            ]);
        }

        $assessment->delete();

        return redirect()
            ->route('admin.assessments.index')
            ->with('status', 'Assessment deleted successfully.');
    }

    private function validateAssessment(Request $request, ?Assessment $assessment = null): array
    {
        $uniquePractice = Rule::unique('assessments', 'practice_resource_id');

        if ($assessment) {
            $uniquePractice->ignore($assessment->id);
        }

        $validated = $request->validate([
            'practice_resource_id' => ['required', 'integer', Rule::exists('practice_resources', 'id'), $uniquePractice],
            'passing_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ]);

        $practiceResource = PracticeResource::query()
            ->whereKey($validated['practice_resource_id'])
            ->where('kind', 'practice')
            ->whereIn('resource_type', ['quiz', 'exercise'])
            ->first();

        if (! $practiceResource) {
            throw ValidationException::withMessages([
                'practice_resource_id' => 'Assessments can only be attached to Practice items with type Quiz or Exercise.',
            ]);
        }

        return $validated;
    }

    private function withBooleanSettings(Request $request, array $validated): array
    {
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['show_feedback'] = $request->boolean('show_feedback');
        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }

    private function eligiblePracticeResources(?Assessment $currentAssessment = null)
    {
        return PracticeResource::query()
            ->where('kind', 'practice')
            ->whereIn('resource_type', ['quiz', 'exercise'])
            ->where(function ($query) use ($currentAssessment) {
                $query->whereDoesntHave('assessment');

                if ($currentAssessment) {
                    $query->orWhereKey($currentAssessment->practice_resource_id);
                }
            })
            ->with('lesson.unit.course.subject')
            ->orderBy('lesson_id')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }
}
