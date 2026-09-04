<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::query()->with('subject');

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
            $query->where('subject_id', $request->integer('subject'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        if ($request->input('featured') === '1') {
            $query->where('is_featured', true);
        }

        return view('admin.courses.index', [
            'courses' => $query
                ->orderBy('subject_id')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(20)
                ->withQueryString(),
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'totalCourses' => Course::query()->count(),
            'publishedCourses' => Course::query()->where('is_published', true)->count(),
            'draftCourses' => Course::query()->where('is_published', false)->count(),
            'featuredCourses' => Course::query()->where('is_featured', true)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.courses.create', [
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title'))),
        ]);

        $validated = $this->validateCourse($request);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_featured'] = $request->boolean('is_featured');

        Course::create($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('status', 'Course created successfully.');
    }

    public function edit(Course $course): View
    {
        return view('admin.courses.edit', [
            'course' => $course,
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title'))),
        ]);

        $validated = $this->validateCourse($request, $course);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_featured'] = $request->boolean('is_featured');

        $course->update($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('status', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('status', 'Course deleted successfully.');
    }

    private function validateCourse(Request $request, ?Course $course = null): array
    {
        $subjectId = $request->integer('subject_id');

        return $request->validate([
            'subject_id' => ['required', 'integer', Rule::exists('subjects', 'id')],
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'required',
                'string',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('courses', 'slug')
                    ->where(fn ($query) => $query->where('subject_id', $subjectId))
                    ->ignore($course?->id),
            ],
            'level' => ['required', Rule::in(['Beginner', 'Intermediate', 'Advanced'])],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
