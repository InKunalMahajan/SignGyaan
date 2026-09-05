<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContentBlock;
use App\Models\MediaAsset;
use App\Models\PracticeResource;
use App\Models\Unit;
use App\Models\VocabularyTerm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                            'contentBlocks' => fn ($blockQuery) => $blockQuery
                                ->with(['mediaAsset', 'practiceResource'])
                                ->orderBy('sort_order')
                                ->orderBy('id'),
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
        $contentBlocks = $lessons->flatMap->contentBlocks;
        $assessments = $practiceResources
            ->map->assessment
            ->filter()
            ->values();

        return view('admin.courses.builder', [
            'course' => $course,
            'lessons' => $lessons,
            'practiceResources' => $practiceResources,
            'contentBlocks' => $contentBlocks,
            'mediaAssets' => MediaAsset::query()
                ->orderBy('media_type')
                ->orderByDesc('is_isl')
                ->orderByDesc('is_published')
                ->orderBy('title')
                ->get(),
            'contentBlockTypes' => LessonContentBlock::TYPES,
            'assessments' => $assessments,
            'totalUnits' => $course->units->count(),
            'publishedUnits' => $course->units->where('is_published', true)->count(),
            'totalLessons' => $lessons->count(),
            'publishedLessons' => $lessons->where('is_published', true)->count(),
            'practiceCount' => $practiceResources->where('kind', 'practice')->count(),
            'resourceCount' => $practiceResources->where('kind', 'resource')->count(),
            'vocabularyCount' => $course->vocabularyTerms->count(),
            'contentBlockCount' => $contentBlocks->count(),
            'assessmentCount' => $assessments->count(),
            'publishedAssessmentCount' => $assessments->where('is_published', true)->count(),
        ]);
    }

    public function quickLesson(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'unit_id' => [
                'required',
                'integer',
                Rule::exists('units', 'id')->where(fn ($query) => $query->where('course_id', $course->id)),
            ],
            'title' => ['required', 'string', 'max:180'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        $unit = Unit::query()
            ->whereKey($validated['unit_id'])
            ->where('course_id', $course->id)
            ->firstOrFail();

        $baseSlug = Str::slug($validated['title']) ?: 'lesson';
        $slug = $baseSlug;
        $suffix = 2;

        while (Lesson::query()->where('unit_id', $unit->id)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $lesson = DB::transaction(function () use ($request, $validated, $unit, $slug) {
            $nextOrder = ((int) Lesson::query()->where('unit_id', $unit->id)->max('sort_order')) + 1;

            return Lesson::create([
                'unit_id' => $unit->id,
                'title' => $validated['title'],
                'slug' => $slug,
                'short_description' => $validated['short_description'] ?? null,
                'estimated_duration_minutes' => $validated['estimated_duration_minutes'] ?? null,
                'sort_order' => $nextOrder,
                'is_published' => $request->boolean('is_published'),
            ]);
        });

        return redirect()
            ->to(route('admin.courses.builder', $course).'#builder-lesson-'.$lesson->id)
            ->with('status', 'Lesson created successfully. You can now add full lesson content.');
    }

    public function reorder(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['units', 'lessons', 'practice', 'vocabulary', 'content_blocks'])],
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
            'content_blocks' => $this->contentBlockOrderingContext($course, $parentId),
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
        $lesson = $this->courseLesson($course, $lessonId);

        return [
            PracticeResource::query()->where('lesson_id', $lesson->id)->pluck('id')->map(fn ($id) => (int) $id)->values(),
            PracticeResource::class,
        ];
    }

    private function contentBlockOrderingContext(Course $course, ?int $lessonId): array
    {
        $lesson = $this->courseLesson($course, $lessonId);

        return [
            LessonContentBlock::query()->where('lesson_id', $lesson->id)->pluck('id')->map(fn ($id) => (int) $id)->values(),
            LessonContentBlock::class,
        ];
    }

    private function courseLesson(Course $course, ?int $lessonId): Lesson
    {
        $lesson = Lesson::query()
            ->whereKey($lessonId)
            ->whereHas('unit', fn ($query) => $query->where('course_id', $course->id))
            ->first();

        if (! $lesson) {
            throw ValidationException::withMessages(['parent_id' => 'The selected lesson does not belong to this course.']);
        }

        return $lesson;
    }
}
