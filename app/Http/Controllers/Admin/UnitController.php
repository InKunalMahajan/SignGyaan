<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $query = Unit::query()->with('course.subject');

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
            $query->whereHas('course', fn ($courseQuery) => $courseQuery->where('subject_id', $subjectId));
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->integer('course'));
        }

        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        return view('admin.units.index', [
            'units' => $query
                ->orderBy('course_id')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->paginate(20)
                ->withQueryString(),
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
            'totalUnits' => Unit::query()->count(),
            'publishedUnits' => Unit::query()->where('is_published', true)->count(),
            'draftUnits' => Unit::query()->where('is_published', false)->count(),
            'coursesWithUnits' => Unit::query()->distinct('course_id')->count('course_id'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.units.create', [
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
            'selectedCourseId' => $request->integer('course') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title'))),
        ]);

        $validated = $this->validateUnit($request);
        $validated['is_published'] = $request->boolean('is_published');

        Unit::create($validated);

        return redirect()
            ->route('admin.units.index')
            ->with('status', 'Unit created successfully.');
    }

    public function edit(Unit $unit): View
    {
        return view('admin.units.edit', [
            'unit' => $unit->load('course.subject'),
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title'))),
        ]);

        $validated = $this->validateUnit($request, $unit);
        $validated['is_published'] = $request->boolean('is_published');

        $unit->update($validated);

        return redirect()
            ->route('admin.units.index')
            ->with('status', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();

        return redirect()
            ->route('admin.units.index')
            ->with('status', 'Unit deleted successfully.');
    }

    private function validateUnit(Request $request, ?Unit $unit = null): array
    {
        $courseId = $request->integer('course_id');
        $uniqueSlug = Rule::unique('units', 'slug')
            ->where(fn ($query) => $query->where('course_id', $courseId));

        if ($unit) {
            $uniqueSlug->ignore($unit->id);
        }

        return $request->validate([
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'required',
                'string',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $uniqueSlug,
            ],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
