<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Unit;
use App\Models\VocabularyTerm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CourseBuilderController extends Controller
{
    public function __invoke(Course $course): View
    {
        $course->load([
            'subject',
            'vocabularyTerms' => fn ($query) => $query
                ->orderBy('sort_order')
                ->orderBy('term'),
            'units' => fn ($unitQuery) => $unitQuery
                ->with([
                    'lessons' => fn ($lessonQuery) => $lessonQuery
                        ->with([
                            'mediaAsset',
                            'vocabularyTerms',
                            'practiceResources' => fn ($practiceQuery) => $practiceQuery
                                ->with([
                                    'assessment' => fn ($assessmentQuery) => $assessmentQuery
                                        ->withCount('questions'),
                                ])
                                ->orderBy('sort_order')
                                ->orderBy('title'),
                        ])
                        ->orderBy('sort_order')
                        ->orderBy('title'),
                ])
                ->orderBy('sort_order')
                ->orderBy('title'),
        ]);

        $lessons = $course->units->flatMap->lessons;
        $practiceResources = $lessons->flatMap->practiceResources;
        $assessments = $practiceResources
            ->map->assessment
            ->filter()
            ->values();

        return view('admin.courses.builder', [
            'course' => $course,
            'lessons' => $lessons,
            'practiceResources' => $practiceResources,
            'assessments' => $assessments,
            'totalUnits' => $course->units->count(),
            'publishedUnits' => $course->units->where('is_published', true)->count(),
            'totalLessons' => $lessons->count(),
            'publishedLessons' => $lessons->where('is_published', true)->count(),
            'practiceCount' => $practiceResources->where('kind', 'practice')->count(),
            'resourceCount' => $practiceResources->where('kind', 'resource')->count(),
            'vocabularyCount' => $course->vocabularyTerms->count(),
            'assessmentCount' => $assessments->count(),
            'publishedAssessmentCount' => $assessments->where('is_published', true)->count(),
        ]);
    }

    public function reorder(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['units', 'lessons', 'practice', 'vocabulary'])],
            'parent_id' => ['nullable', 'integer', 'min:1'],
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'distinct', 'min:1'],
        ]);

        $ids = collect($validated['ids'])->map(fn ($id) => (int) $id)->values();
        $type = $validated['type'];
        $parentId = isset($validated['parent_id']) ? (int) $validated['parent_id'] : null;

        [$currentIds, $model] = match ($type) {
            'units' => [
                Unit::query()->where('course_id', $course->id)->pluck('id')->map(fn ($id) => (int) $id)->values(),
                Unit::class,
            ],
            'lessons' => $this->lessonOrderingContext($course, $parentId),
            'practice' => $this->practiceOrderingContext($course, $parentId),
            'vocabulary' => [
                VocabularyTerm::query()->where('course_id', $course->id)->pluck('id')->map(fn ($id) => (int) $id)->values(),
                VocabularyTerm::class,
            ],
        };

        if ($ids->sort()->values()->all() !== $currentIds->sort()->values()->all()) {
            throw ValidationException::withMessages([
                'ids' => 'The ordering list must contain every current item exactly once.',
            ]);
        }

        DB::transaction(function () use ($ids, $model) {
            $ids->each(function (int $id, int $index) use ($model) {
                $model::query()->whereKey($id)->update(['sort_order' => $index + 1]);
            });
        });

        return response()->json([
            'saved' => true,
            'type' => $type,
            'count' => $ids->count(),
        ]);
    }

    private function lessonOrderingContext(Course $course, ?int $unitId): array
    {
        $unit = Unit::query()
            ->whereKey($unitId)
            ->where('course_id', $course->id)
            ->first();

        if (! $unit) {
            throw ValidationException::withMessages(['parent_id' => 'The selected unit does not belong to this course.']);
        }

        return [
            Lesson::query()->where('unit_id', $unit->id)->pluck('id')->map(fn ($id) => (int) $id)->values(),
            Lesson::class,
        ];
    }

    private function practiceOrderingContext(Course $course, ?int $lessonId): array
    {
        $lesson = Lesson::query()
            ->whereKey($lessonId)
            ->whereHas('unit', fn ($query) => $query->where('course_id', $course->id))
            ->first();

        if (! $lesson) {
            throw ValidationException::withMessages(['parent_id' => 'The selected lesson does not belong to this course.']);
        }

        return [
            PracticeResource::query()->where('lesson_id', $lesson->id)->pluck('id')->map(fn ($id) => (int) $id)->values(),
            PracticeResource::class,
        ];
    }
}
