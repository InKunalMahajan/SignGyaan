<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\Board;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurriculumCatalogController extends Controller
{
    public function index(): View
    {
        $boards = Board::query()
            ->with(['academicClasses.subjects' => fn ($query) => $query->withCount('courses')])
            ->withCount('academicClasses')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $academicClasses = AcademicClass::query()
            ->with('board')
            ->withCount('subjects')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $subjects = Subject::query()
            ->with(['academicClass.board', 'creator'])
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        return view('admin.curriculum.index', compact('boards', 'academicClasses', 'subjects'));
    }

    public function storeBoard(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:boards,name'],
            'short_name' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Board::create([
            ...$validated,
            'slug' => $this->uniqueSlug(Board::class, $validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Board created successfully.');
    }

    public function updateBoard(Request $request, Board $board): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('boards', 'name')->ignore($board)],
            'short_name' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $board->update([
            ...$validated,
            'slug' => $this->uniqueSlug(Board::class, $validated['name'], $board->id),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Board updated successfully.');
    }

    public function destroyBoard(Board $board): RedirectResponse
    {
        if ($board->academicClasses()->exists()) {
            return back()->withErrors(['board' => 'This board cannot be deleted because it still has academic classes.']);
        }

        $board->delete();

        return back()->with('success', 'Board deleted successfully.');
    }

    public function storeAcademicClass(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'board_id' => ['required', 'exists:boards,id'],
            'name' => ['required', 'string', 'max:120'],
            'level' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('academic_classes', 'name')->where(fn ($query) => $query->where('board_id', $validated['board_id'])),
            ],
        ]);

        AcademicClass::create([
            ...$validated,
            'slug' => $this->uniqueSlug(AcademicClass::class, $validated['name'], null, ['board_id' => $validated['board_id']]),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Academic class created successfully.');
    }

    public function updateAcademicClass(Request $request, AcademicClass $academicClass): RedirectResponse
    {
        $validated = $request->validate([
            'board_id' => ['required', 'exists:boards,id'],
            'name' => ['required', 'string', 'max:120'],
            'level' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('academic_classes', 'name')
                    ->where(fn ($query) => $query->where('board_id', $validated['board_id']))
                    ->ignore($academicClass),
            ],
        ]);

        $academicClass->update([
            ...$validated,
            'slug' => $this->uniqueSlug(AcademicClass::class, $validated['name'], $academicClass->id, ['board_id' => $validated['board_id']]),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Academic class updated successfully.');
    }

    public function destroyAcademicClass(AcademicClass $academicClass): RedirectResponse
    {
        if ($academicClass->subjects()->exists()) {
            return back()->withErrors(['academic_class' => 'This academic class cannot be deleted because it still has subjects.']);
        }

        $academicClass->delete();

        return back()->with('success', 'Academic class deleted successfully.');
    }

    public function storeSubject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_class_id' => ['required', 'exists:academic_classes,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('subjects', 'name')->where(fn ($query) => $query->where('academic_class_id', $validated['academic_class_id'])),
            ],
        ]);

        Subject::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'slug' => $this->uniqueSlug(Subject::class, $validated['name'], null, ['academic_class_id' => $validated['academic_class_id']]),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Subject created successfully.');
    }

    public function updateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'academic_class_id' => ['required', 'exists:academic_classes,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('subjects', 'name')
                    ->where(fn ($query) => $query->where('academic_class_id', $validated['academic_class_id']))
                    ->ignore($subject),
            ],
        ]);

        $subject->update([
            ...$validated,
            'slug' => $this->uniqueSlug(Subject::class, $validated['name'], $subject->id, ['academic_class_id' => $validated['academic_class_id']]),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Subject updated successfully.');
    }

    public function destroySubject(Subject $subject): RedirectResponse
    {
        if ($subject->courses()->exists()) {
            return back()->withErrors(['subject' => 'This subject cannot be deleted because it still has courses.']);
        }

        $subject->delete();

        return back()->with('success', 'Subject deleted successfully.');
    }

    private function uniqueSlug(string $modelClass, string $name, ?int $ignoreId = null, array $scope = []): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $counter = 2;

        while ($modelClass::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->when($scope, function ($query) use ($scope) {
                foreach ($scope as $column => $value) {
                    $query->where($column, $value);
                }
            })
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
