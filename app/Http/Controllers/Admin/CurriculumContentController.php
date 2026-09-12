<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Lesson;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurriculumContentController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::query()
            ->with([
                'academicClass.board',
                'courses' => fn ($query) => $query
                    ->with([
                        'creator',
                        'units' => fn ($unitQuery) => $unitQuery
                            ->with(['lessons' => fn ($lessonQuery) => $lessonQuery
                                ->withCount('progress')
                                ->orderBy('position')
                                ->orderBy('id')])
                            ->orderBy('position')
                            ->orderBy('id'),
                    ])
                    ->withCount(['units', 'classes'])
                    ->orderBy('title'),
            ])
            ->orderBy('name')
            ->get();

        $courses = Course::query()
            ->with(['subject.academicClass.board', 'creator'])
            ->withCount(['units', 'classes'])
            ->orderBy('title')
            ->get();

        return view('admin.curriculum.content', compact('subjects', 'courses'));
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:160'],
            'level' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:3000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->validate([
            'title' => [
                Rule::unique('courses', 'title')
                    ->where(fn ($query) => $query->where('subject_id', $validated['subject_id'])),
            ],
        ]);

        Course::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'slug' => $this->uniqueCourseSlug($validated['title']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Course created successfully.');
    }

    public function updateCourse(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:160'],
            'level' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:3000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->validate([
            'title' => [
                Rule::unique('courses', 'title')
                    ->where(fn ($query) => $query->where('subject_id', $validated['subject_id']))
                    ->ignore($course),
            ],
        ]);

        $course->update([
            ...$validated,
            'slug' => $this->uniqueCourseSlug($validated['title'], $course->id),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Course updated successfully.');
    }

    public function destroyCourse(Course $course): RedirectResponse
    {
        if ($course->classes()->exists()) {
            return back()->withErrors(['course' => 'This course cannot be deleted because it is still assigned to learning classes.']);
        }

        if ($course->units()->exists()) {
            return back()->withErrors(['course' => 'This course cannot be deleted because it still has chapters or units.']);
        }

        $course->delete();

        return back()->with('success', 'Course deleted successfully.');
    }

    public function storeUnit(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'position' => ['nullable', 'integer', 'min:1', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $position = $validated['position'] ?? ((int) $course->units()->max('position') + 1);

        $course->units()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'position' => max(1, $position),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Chapter / unit created successfully.');
    }

    public function updateUnit(Request $request, Course $course, CourseUnit $unit): RedirectResponse
    {
        $this->ensureUnitBelongsToCourse($course, $unit);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'position' => ['required', 'integer', 'min:1', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $unit->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Chapter / unit updated successfully.');
    }

    public function destroyUnit(Course $course, CourseUnit $unit): RedirectResponse
    {
        $this->ensureUnitBelongsToCourse($course, $unit);

        if ($unit->lessons()->exists()) {
            return back()->withErrors(['unit' => 'This chapter / unit cannot be deleted because it still has lessons.']);
        }

        $unit->delete();

        return back()->with('success', 'Chapter / unit deleted successfully.');
    }

    public function storeLesson(Request $request, Course $course, CourseUnit $unit): RedirectResponse
    {
        $this->ensureUnitBelongsToCourse($course, $unit);

        $validated = $request->validate($this->lessonRules(false));
        $position = $validated['position'] ?? ((int) $unit->lessons()->max('position') + 1);

        $unit->lessons()->create([
            ...$validated,
            'position' => max(1, $position),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        return back()->with('success', 'Lesson created successfully.');
    }

    public function updateLesson(
        Request $request,
        Course $course,
        CourseUnit $unit,
        Lesson $lesson
    ): RedirectResponse {
        $this->ensureUnitBelongsToCourse($course, $unit);
        $this->ensureLessonBelongsToUnit($unit, $lesson);

        $validated = $request->validate($this->lessonRules(true));
        $wasPublished = $lesson->isPublished();
        $willBePublished = $validated['status'] === 'published';

        $lesson->update([
            ...$validated,
            'published_at' => match (true) {
                $willBePublished && ! $wasPublished => now(),
                ! $willBePublished => null,
                default => $lesson->published_at,
            },
        ]);

        return back()->with('success', 'Lesson updated successfully.');
    }

    public function destroyLesson(Course $course, CourseUnit $unit, Lesson $lesson): RedirectResponse
    {
        $this->ensureUnitBelongsToCourse($course, $unit);
        $this->ensureLessonBelongsToUnit($unit, $lesson);

        if ($lesson->progress()->exists()) {
            return back()->withErrors(['lesson' => 'This lesson cannot be deleted because learner progress is already recorded.']);
        }

        $lesson->delete();

        return back()->with('success', 'Lesson deleted successfully.');
    }

    private function ensureUnitBelongsToCourse(Course $course, CourseUnit $unit): void
    {
        abort_unless($unit->course_id === $course->id, 404);
    }

    private function ensureLessonBelongsToUnit(CourseUnit $unit, Lesson $lesson): void
    {
        abort_unless($lesson->course_unit_id === $unit->id, 404);
    }

    private function lessonRules(bool $requiresPosition): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['nullable', 'string', 'max:3000'],
            'isl_video_url' => ['nullable', 'url', 'max:500'],
            'notes' => ['nullable', 'string', 'max:20000'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'position' => [$requiresPosition ? 'required' : 'nullable', 'integer', 'min:1', 'max:999'],
            'status' => ['required', Rule::in(Lesson::STATUSES)],
        ];
    }

    private function uniqueCourseSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'course';
        $slug = $base;
        $counter = 2;

        while (Course::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
