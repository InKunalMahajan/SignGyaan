<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lesson::query()->with('unit.course.subject');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject')) {
            $subjectId = $request->integer('subject');
            $query->whereHas('unit.course', fn ($courseQuery) => $courseQuery->where('subject_id', $subjectId));
        }

        if ($request->filled('course')) {
            $courseId = $request->integer('course');
            $query->whereHas('unit', fn ($unitQuery) => $unitQuery->where('course_id', $courseId));
        }

        if ($request->filled('unit')) {
            $query->where('unit_id', $request->integer('unit'));
        }

        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        if ($request->input('video') === 'with') {
            $query->whereNotNull('isl_video_url')->where('isl_video_url', '!=', '');
        } elseif ($request->input('video') === 'without') {
            $query->where(function ($builder) {
                $builder->whereNull('isl_video_url')->orWhere('isl_video_url', '');
            });
        }

        return view('admin.lessons.index', [
            'lessons' => $query
                ->orderBy('unit_id')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(20)
                ->withQueryString(),
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
            'units' => Unit::query()->with('course.subject')->orderBy('course_id')->orderBy('sort_order')->orderBy('title')->get(),
            'totalLessons' => Lesson::query()->count(),
            'publishedLessons' => Lesson::query()->where('is_published', true)->count(),
            'draftLessons' => Lesson::query()->where('is_published', false)->count(),
            'lessonsWithVideo' => Lesson::query()->whereNotNull('isl_video_url')->where('isl_video_url', '!=', '')->count(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.lessons.create', [
            'units' => Unit::query()->with('course.subject')->orderBy('course_id')->orderBy('sort_order')->orderBy('title')->get(),
            'selectedUnitId' => $request->integer('unit') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title'))),
        ]);

        $validated = $this->validateLesson($request);
        $validated['is_published'] = $request->boolean('is_published');

        Lesson::create($validated);

        return redirect()
            ->route('admin.lessons.index')
            ->with('status', 'Lesson created successfully.');
    }

    public function edit(Lesson $lesson): View
    {
        return view('admin.lessons.edit', [
            'lesson' => $lesson->load('unit.course.subject'),
            'units' => Unit::query()->with('course.subject')->orderBy('course_id')->orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title'))),
        ]);

        $validated = $this->validateLesson($request, $lesson);
        $validated['is_published'] = $request->boolean('is_published');

        $lesson->update($validated);

        return redirect()
            ->route('admin.lessons.index')
            ->with('status', 'Lesson updated successfully.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        if ($lesson->practiceResources()->exists()) {
            return redirect()
                ->route('admin.lessons.index')
                ->with('status', 'This lesson cannot be deleted while it still contains practice or resource items. Move or delete those items first.');
        }

        $lesson->delete();

        return redirect()
            ->route('admin.lessons.index')
            ->with('status', 'Lesson deleted successfully.');
    }

    private function validateLesson(Request $request, ?Lesson $lesson = null): array
    {
        $unitId = $request->integer('unit_id');
        $uniqueSlug = Rule::unique('lessons', 'slug')
            ->where(fn ($query) => $query->where('unit_id', $unitId));

        if ($lesson) {
            $uniqueSlug->ignore($lesson->id);
        }

        return $request->validate([
            'unit_id' => ['required', 'integer', Rule::exists('units', 'id')],
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'required',
                'string',
                'max:200',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $uniqueSlug,
            ],
            'short_description' => ['nullable', 'string', 'max:255'],
            'learning_objectives' => ['nullable', 'string', 'max:50000'],
            'content' => ['nullable', 'string', 'max:100000'],
            'key_points' => ['nullable', 'string', 'max:50000'],
            'example_content' => ['nullable', 'string', 'max:50000'],
            'isl_video_url' => ['nullable', 'url', 'max:2048'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
