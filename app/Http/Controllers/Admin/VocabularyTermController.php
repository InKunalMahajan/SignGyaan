<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\MediaAsset;
use App\Models\Subject;
use App\Models\VocabularyTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VocabularyTermController extends Controller
{
    public function index(Request $request): View
    {
        $query = VocabularyTerm::query()->with(['subject', 'course', 'mediaAsset'])->withCount('lessons');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($search) {
                $builder->where('term', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('meaning', 'like', "%{$search}%")
                    ->orWhere('example', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject')) {
            $query->where('subject_id', $request->integer('subject'));
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->integer('course'));
        }

        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        if ($request->input('video') === 'with') {
            $query->where(function ($builder) {
                $builder->whereNotNull('isl_media_asset_id')
                    ->orWhere(function ($urlQuery) {
                        $urlQuery->whereNotNull('isl_video_url')->where('isl_video_url', '!=', '');
                    });
            });
        } elseif ($request->input('video') === 'without') {
            $query->whereNull('isl_media_asset_id')
                ->where(function ($builder) {
                    $builder->whereNull('isl_video_url')->orWhere('isl_video_url', '');
                });
        }

        return view('admin.vocabulary.index', [
            'terms' => $query->orderBy('sort_order')->orderBy('term')->paginate(24)->withQueryString(),
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
            'totalTerms' => VocabularyTerm::query()->count(),
            'publishedTerms' => VocabularyTerm::query()->where('is_published', true)->count(),
            'termsWithVideo' => VocabularyTerm::query()->where(function ($builder) {
                $builder->whereNotNull('isl_media_asset_id')
                    ->orWhere(function ($urlQuery) {
                        $urlQuery->whereNotNull('isl_video_url')->where('isl_video_url', '!=', '');
                    });
            })->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.vocabulary.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['slug' => Str::slug((string) ($request->input('slug') ?: $request->input('term')))]);
        $validated = $this->validateTerm($request);
        $lessonIds = $validated['lesson_ids'] ?? [];
        unset($validated['lesson_ids']);
        $validated['is_published'] = $request->boolean('is_published');

        $term = VocabularyTerm::create($validated);
        $this->syncLessons($term, $lessonIds);

        return redirect()->route('admin.vocabulary.index')->with('status', 'Vocabulary term created successfully.');
    }

    public function edit(VocabularyTerm $vocabulary): View
    {
        return view('admin.vocabulary.edit', $this->formData() + [
            'vocabulary' => $vocabulary->load(['subject', 'course', 'mediaAsset', 'lessons.unit.course.subject']),
        ]);
    }

    public function update(Request $request, VocabularyTerm $vocabulary): RedirectResponse
    {
        $request->merge(['slug' => Str::slug((string) ($request->input('slug') ?: $request->input('term')))]);
        $validated = $this->validateTerm($request, $vocabulary);
        $lessonIds = $validated['lesson_ids'] ?? [];
        unset($validated['lesson_ids']);
        $validated['is_published'] = $request->boolean('is_published');

        $vocabulary->update($validated);
        $this->syncLessons($vocabulary, $lessonIds);

        return redirect()->route('admin.vocabulary.index')->with('status', 'Vocabulary term updated successfully.');
    }

    public function destroy(VocabularyTerm $vocabulary): RedirectResponse
    {
        $vocabulary->delete();

        return redirect()->route('admin.vocabulary.index')->with('status', 'Vocabulary term deleted successfully.');
    }

    private function validateTerm(Request $request, ?VocabularyTerm $term = null): array
    {
        $subjectId = $request->integer('subject_id') ?: null;

        $slugRule = Rule::unique('vocabulary_terms', 'slug');
        if ($term) {
            $slugRule->ignore($term->id);
        }

        return $request->validate([
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')],
            'course_id' => [
                'nullable',
                'integer',
                Rule::exists('courses', 'id')->when($subjectId, fn ($query) => $query->where('subject_id', $subjectId)),
            ],
            'term' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugRule],
            'meaning' => ['nullable', 'string', 'max:10000'],
            'example' => ['nullable', 'string', 'max:10000'],
            'isl_media_asset_id' => [
                'nullable',
                'integer',
                Rule::exists('media_assets', 'id')->where(fn ($query) => $query->where('media_type', 'video')->where('is_isl', true)),
            ],
            'isl_video_url' => ['nullable', 'url', 'max:2048'],
            'lesson_ids' => ['nullable', 'array', 'max:200'],
            'lesson_ids.*' => ['integer', 'distinct', Rule::exists('lessons', 'id')],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);
    }

    private function formData(): array
    {
        return [
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
            'lessons' => Lesson::query()
                ->with('unit.course.subject')
                ->orderBy('unit_id')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get(),
            'mediaAssets' => MediaAsset::query()
                ->where('media_type', 'video')
                ->where('is_isl', true)
                ->orderByDesc('is_published')
                ->orderBy('title')
                ->get(),
        ];
    }

    private function syncLessons(VocabularyTerm $term, array $lessonIds): void
    {
        $syncData = [];

        foreach (array_values($lessonIds) as $index => $lessonId) {
            $syncData[(int) $lessonId] = ['sort_order' => $index];
        }

        $term->lessons()->sync($syncData);
    }
}
