<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $classes = LearningClass::query()
            ->where('teacher_id', $request->user()->id)
            ->withCount(['learners', 'courses'])
            ->latest()
            ->paginate(12);

        return view('teacher.classes.index', [
            'classes' => $classes,
            'activeCount' => LearningClass::where('teacher_id', $request->user()->id)->where('is_active', true)->count(),
            'totalLearners' => $request->user()->teachingClasses()
                ->withCount('learners')
                ->get()
                ->sum('learners_count'),
        ]);
    }

    public function create(): View
    {
        return view('teacher.classes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->classRules());

        $class = LearningClass::create([
            ...$validated,
            'teacher_id' => $request->user()->id,
            'code' => $this->generateClassCode(),
            'is_active' => true,
        ]);

        return redirect()->route('teacher.classes.show', $class)
            ->with('status', 'Class created successfully.');
    }

    public function show(Request $request, LearningClass $class): View
    {
        $this->authorizeOwner($request, $class);

        $class->load([
            'learners' => fn ($query) => $query
                ->with('learnerProfile')
                ->orderBy('name'),
            'courses' => fn ($query) => $query
                ->with('subject')
                ->orderBy('title'),
        ]);

        $availableCourses = Course::query()
            ->where('is_active', true)
            ->whereHas('subject', fn ($query) => $query->where('is_active', true))
            ->whereNotIn('id', $class->courses->pluck('id'))
            ->with('subject')
            ->orderBy('title')
            ->get();

        return view('teacher.classes.show', [
            'class' => $class,
            'availableCourses' => $availableCourses,
        ]);
    }

    public function edit(Request $request, LearningClass $class): View
    {
        $this->authorizeOwner($request, $class);

        return view('teacher.classes.edit', [
            'class' => $class,
        ]);
    }

    public function update(Request $request, LearningClass $class): RedirectResponse
    {
        $this->authorizeOwner($request, $class);

        $class->update($request->validate($this->classRules()));

        return redirect()->route('teacher.classes.show', $class)
            ->with('status', 'Class details updated.');
    }

    public function toggleStatus(Request $request, LearningClass $class): RedirectResponse
    {
        $this->authorizeOwner($request, $class);

        $class->update([
            'is_active' => ! $class->is_active,
        ]);

        return back()->with(
            'status',
            $class->is_active ? 'Class activated.' : 'Class archived.'
        );
    }

    public function enrollLearner(Request $request, LearningClass $class): RedirectResponse
    {
        $this->authorizeOwner($request, $class);

        if (! $class->is_active) {
            throw ValidationException::withMessages([
                'learner_email' => 'Activate this class before adding Learners.',
            ]);
        }

        $validated = $request->validate([
            'learner_email' => ['required', 'email', 'max:255'],
        ]);

        $learner = User::query()
            ->where('email', $validated['learner_email'])
            ->where('role', 'learner')
            ->where('is_active', true)
            ->first();

        if (! $learner) {
            throw ValidationException::withMessages([
                'learner_email' => 'No active Learner account was found with that email address.',
            ]);
        }

        if ($class->learners()->whereKey($learner->id)->exists()) {
            throw ValidationException::withMessages([
                'learner_email' => 'This Learner is already enrolled in the class.',
            ]);
        }

        $class->learners()->attach($learner->id, [
            'enrolled_at' => now(),
        ]);

        return back()->with('status', "{$learner->name} was added to the class.");
    }

    public function removeLearner(Request $request, LearningClass $class, User $learner): RedirectResponse
    {
        $this->authorizeOwner($request, $class);

        abort_unless($learner->hasRole('learner'), 404);
        abort_unless($class->learners()->whereKey($learner->id)->exists(), 404);

        $class->learners()->detach($learner->id);

        return back()->with('status', "{$learner->name} was removed from the class.");
    }

    private function authorizeOwner(Request $request, LearningClass $class): void
    {
        abort_unless($class->teacher_id === $request->user()->id, 403);
    }

    private function classRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'subject' => ['nullable', 'string', 'max:120'],
            'level' => ['nullable', 'string', 'max:100'],
            'academic_year' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function generateClassCode(): string
    {
        do {
            $code = 'SG-'.Str::upper(Str::random(6));
        } while (LearningClass::where('code', $code)->exists());

        return $code;
    }
}
