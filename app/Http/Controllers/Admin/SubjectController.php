<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Subject::query();

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        }

        if ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        return view('admin.subjects.index', [
            'subjects' => $query
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
            'totalSubjects' => Subject::query()->count(),
            'publishedSubjects' => Subject::query()->where('is_published', true)->count(),
            'draftSubjects' => Subject::query()->where('is_published', false)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('name'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('subjects', 'slug')],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        Subject::create($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('status', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug((string) ($request->input('slug') ?: $request->input('name'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:140',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('subjects', 'slug')->ignore($subject->id),
            ],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $subject->update($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('status', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('status', 'Subject deleted successfully.');
    }
}
