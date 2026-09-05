<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\MediaAsset;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PracticeResourceController extends Controller
{
    public function index(Request $request): View
    {
        $query = PracticeResource::query()->with(['lesson.unit.course.subject', 'mediaAsset']);

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('instructions', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject')) {
            $subjectId = $request->integer('subject');
            $query->whereHas('lesson.unit.course', fn ($courseQuery) => $courseQuery->where('subject_id', $subjectId));
        }

        if ($request->filled('course')) {
            $courseId = $request->integer('course');
            $query->whereHas('lesson.unit', fn ($unitQuery) => $unitQuery->where('course_id', $courseId));
        }

        if ($request->filled('lesson')) {
            $query->where('lesson_id', $request->integer('lesson'));
        }

        if (in_array($request->input('kind'), ['practice', 'resource'], true)) {
            $query->where('kind', $request->input('kind'));
        }

        if ($request->filled('type')) {
            $query->where('resource_type', $request->input('type'));
        }

        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        return view('admin.practice-resources.index', [
            'items' => $query
                ->orderBy('lesson_id')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(20)
                ->withQueryString(),
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
            'lessons' => Lesson::query()->with('unit.course.subject')->orderBy('unit_id')->orderBy('sort_order')->orderBy('title')->get(),
            'totalItems' => PracticeResource::query()->count(),
            'practiceCount' => PracticeResource::query()->where('kind', 'practice')->count(),
            'resourceCount' => PracticeResource::query()->where('kind', 'resource')->count(),
            'publishedCount' => PracticeResource::query()->where('is_published', true)->count(),
            'resourceTypes' => $this->resourceTypes(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.practice-resources.create', [
            'lessons' => Lesson::query()->with('unit.course.subject')->orderBy('unit_id')->orderBy('sort_order')->orderBy('title')->get(),
            'selectedLessonId' => $request->integer('lesson') ?: null,
            'resourceTypes' => $this->resourceTypes(),
            'mediaAssets' => $this->mediaAssets(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title'))),
        ]);

        $validated = $this->validateItem($request);
        $validated['is_published'] = $request->boolean('is_published');

        PracticeResource::create($validated);

        return redirect()
            ->route('admin.practice.index')
            ->with('status', 'Practice or resource item created successfully.');
    }

    public function edit(PracticeResource $practiceResource): View
    {
        return view('admin.practice-resources.edit', [
            'item' => $practiceResource->load(['lesson.unit.course.subject', 'mediaAsset']),
            'lessons' => Lesson::query()->with('unit.course.subject')->orderBy('unit_id')->orderBy('sort_order')->orderBy('title')->get(),
            'resourceTypes' => $this->resourceTypes(),
            'mediaAssets' => $this->mediaAssets(),
        ]);
    }

    public function update(Request $request, PracticeResource $practiceResource): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title'))),
        ]);

        $validated = $this->validateItem($request, $practiceResource);

        if (
            $practiceResource->assessment()->exists()
            && ($validated['kind'] !== 'practice' || ! in_array($validated['resource_type'], ['quiz', 'exercise'], true))
        ) {
            return back()
                ->withErrors([
                    'resource_type' => 'This item is linked to an assessment. Keep it as a Practice item with type Quiz or Exercise, or remove the assessment first.',
                ])
                ->withInput();
        }

        $validated['is_published'] = $request->boolean('is_published');

        $practiceResource->update($validated);

        return redirect()
            ->route('admin.practice.index')
            ->with('status', 'Practice or resource item updated successfully.');
    }

    public function destroy(PracticeResource $practiceResource): RedirectResponse
    {
        if ($practiceResource->assessment()->exists()) {
            return back()->with('status', 'This practice item is linked to an assessment. Delete the assessment first before deleting the practice item.');
        }

        $practiceResource->delete();

        return redirect()
            ->route('admin.practice.index')
            ->with('status', 'Practice or resource item deleted successfully.');
    }

    private function validateItem(Request $request, ?PracticeResource $item = null): array
    {
        $lessonId = $request->integer('lesson_id');
        $uniqueSlug = Rule::unique('practice_resources', 'slug')
            ->where(fn ($query) => $query->where('lesson_id', $lessonId));

        if ($item) {
            $uniqueSlug->ignore($item->id);
        }

        return $request->validate([
            'lesson_id' => ['required', 'integer', Rule::exists('lessons', 'id')],
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'required',
                'string',
                'max:200',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $uniqueSlug,
            ],
            'kind' => ['required', Rule::in(['practice', 'resource'])],
            'resource_type' => ['required', Rule::in(array_keys($this->resourceTypes()))],
            'short_description' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:50000'],
            'content' => ['nullable', 'string', 'max:100000'],
            'answer_key' => ['nullable', 'string', 'max:100000'],
            'resource_url' => ['nullable', 'url', 'max:2048'],
            'media_asset_id' => ['nullable', 'integer', Rule::exists('media_assets', 'id')],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);
    }

    private function mediaAssets()
    {
        return MediaAsset::query()
            ->orderByDesc('is_published')
            ->orderBy('media_type')
            ->orderBy('title')
            ->get();
    }

    private function resourceTypes(): array
    {
        return [
            'exercise' => 'Exercise',
            'quiz' => 'Quiz',
            'reflection' => 'Reflection',
            'worksheet' => 'Worksheet',
            'notes' => 'Notes / Handout',
            'download' => 'Download',
            'external-link' => 'External Link',
            'reference' => 'Reference',
        ];
    }
}
